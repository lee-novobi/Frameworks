package Watcher;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Helper.MyMySQL;
import Helper.Utils;
import Model.IncidentModel;

import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.text.DateFormat;
import java.text.SimpleDateFormat;

import java.sql.*;

public class SyncherIncident {
	public void SynchFromITSM(){
		try{
			HashMap<String, HashMap<String, String>> mDeptToSMS = new HashMap<String, HashMap<String, String>>();
			int nSMSTimeoutMinutes = Config.DEFAULT_INCIDENT_SMS_NOTIFY_TIMEOUT;

		    String lastSynch     = Config.SynchFileGetProperty("INCIDENT_LAST_SYNCH");
		    String synchDistance = Config.SynchFileGetProperty("INCIDENT_SYNCH_DISTANCE");
		    String specificList  = Config.SynchFileGetProperty("INCIDENT_SYNCH_SPECIFIC");
		    String debugMode     = Config.SynchFileGetProperty("INCIDENT_DEBUG");
		    
		    try{
		    	String strSMSNotifyTo= Config.SynchFileGetProperty("INCIDENT_SMS_NOTIFY_TO");
		    	
		    	if(null != strSMSNotifyTo && !strSMSNotifyTo.isEmpty()){
		    		String strSMSTimeout = Config.SynchFileGetProperty("INCIDENT_SMS_NOTIFY_TIMEOUT");
		    		if(null != strSMSNotifyTo && !strSMSNotifyTo.isEmpty()){
		    			try{
		    				nSMSTimeoutMinutes = Integer.parseInt(strSMSTimeout);
		    			}
		    			catch(Exception e)
		    	        {
		    	            System.err.println((new StringBuilder("SynchFromITSM: Parse INCIDENT_SMS_NOTIFY_MAX_DELAY Exception: ")).append(e.getMessage()).toString());
		    	        }
		    		}
			    	String[] arrSMSReceiverGroup = strSMSNotifyTo.split(";");
			    	for(String strSMSReceiverGroup:arrSMSReceiverGroup){
			    		HashMap<String, String> mUserToSMS = null;
			    		String[] arrSMSReceiverGroupDetail = strSMSReceiverGroup.split(":");
			    		
			    		String strDepartment = arrSMSReceiverGroupDetail[0].toLowerCase();
			    		String[] arrReceiverClassifyInfo = arrSMSReceiverGroupDetail[1].split("=");
			    		String[] arrReceiverUser = arrReceiverClassifyInfo[0].split(",");
			    		
			    		if(!mDeptToSMS.containsKey(strDepartment)){
			    			mUserToSMS = new HashMap<String, String>();
			    			mDeptToSMS.put(strDepartment, mUserToSMS);
			    		} else {
			    			mUserToSMS = mDeptToSMS.get(strDepartment);
			    		}
			    		for(String strUser:arrReceiverUser){
			    			mUserToSMS.put(strUser, arrReceiverClassifyInfo[1]);
			    		}
			    	}
			    }
		    } catch(Exception e) {
				String s = "SynchFromITSM Exception: " + e.getMessage();
				System.err.println(s);
				MyLogger.WriteLog("EXCEPTION", s);
			}
		    
		    boolean run          = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_INCIDENT"));
		    
		    if(run){
			    SynchFromITSM(lastSynch, synchDistance, specificList, mDeptToSMS, nSMSTimeoutMinutes, debugMode);
			    
			    DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("INCIDENT_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "Incident Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "SynchFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
	
	private void SynchFromITSM(String lastSynch, String synchDistance, String specificList, HashMap<String, HashMap<String, String>> mSMSNotifyTo, int nSMSTimeoutMinutes, String debugMode){
		// General synch
		try{
			MyMSSQL itsmDB = new MyMSSQL();
			MyMySQL sdkDB  = new MyMySQL();
			
			DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			Calendar c1          = Calendar.getInstance();
			Date lastSynchTime   = new Date();
			int distance         = Config.WATCHER_SYNCH_BACK_DISTANCE; // Default
			
			try{
				distance = Integer.parseInt(synchDistance);
			} catch (Exception e){}
			try{
				lastSynchTime = (Date)formatter.parse(lastSynch);
			} catch (Exception e){}
			
			c1.setTime(lastSynchTime);
			c1.add(Calendar.MINUTE, -distance);
			c1.add(Calendar.HOUR, -7);
			
			String synchFrom = formatter.format(c1.getTime());
			
			if(itsmDB.TestConnection()){
				String count_query = String.format("SELECT COUNT(1) AS waiting_count FROM %s WHERE ([UPDATE TIME]>='%s') OR ([UPDATE TIME] IS NULL AND [OPEN TIME]>='%s')",
						Config.MSSQL_TBL_INCIDENT, synchFrom, synchFrom);
				
				ResultSet rs = itsmDB.ExecuteQuery(count_query);
				if(rs != null){
					rs.next();
					int waiting_count = rs.getInt("waiting_count");
					
					if(waiting_count > 0){
						StringBuilder query = new StringBuilder();
						query.append("SELECT ");
						query.append("CASE WHEN [OUTAGE END] IS NOT NULL THEN 'closed' ELSE NULL END AS [INTERNAL_STATUS],");
						query.append("[INC_ID],[PRODUCT],[TITLE],[IMPACT],[OUTAGE START],[OUTAGE END],");
						query.append("[INC_STATUS],[AREA],[SUB_AREA],[DESCRIPTION],[SOLUTION],[PRIORITY_CODE],");
						query.append("[OPEN TIME],[OPENED_BY],[PROBSUMMARYM2].[RESOLVE_BY_SDK],[UPDATE TIME],[UPDATE_ACTION],");
						query.append("[UPDATED_BY],[ASSIGNMENT],[ASSIGNEE_NAME],[PROBSUMMARYM2].[CAUSED_BY_EXTERNAL_SERVICE],[PROBSUMMARYM2].[LOCATION_FULL_NAME],");
						query.append("[PROBSUMMARYM2].[USER_IMPACTED],[PROBSUMMARYM2].[SDK_INCIDENT_DETECTOR],[PROBSUMMARYM2].[CREATED_BY],[DEPARTMENT],[PROBSUMMARYM2].[CAUSED_BY_DEPT],[PROBSUMMARYM2].[REOPENED_BY],[REOPEN TIME],");
						query.append("[PROBSUMMARYM2].[NOTE_SDK],[PROBSUMMARYM2].[CUSTOMER_IMPACTED],[PROBSUMMARYM2].[DOWN_START],[PROBSUMMARYM2].[USER_CCUTIME],[PROBSUMMARYM2].[ROOTCAUSE_CATEGORY],[ROOT_CAUSE],[PROBSUMMARYM2].[RELATED_ID],[PROBSUMMARYM2].[RELATED_ID_CHANGE],[PROBSUMMARYM2].[IS_DOWNTIME] ");
						query.append(String.format("FROM %s INNER JOIN [PROBSUMMARYM2] ON([PROBSUMMARYM2].NUMBER=%s.INC_ID) WHERE ([UPDATE TIME]>='%s') OR ([UPDATE TIME] IS NULL AND [OPEN TIME]>='%s')",
								Config.MSSQL_TBL_INCIDENT, Config.MSSQL_TBL_INCIDENT, synchFrom, synchFrom));
						
						rs = itsmDB.ExecuteQuery(query.toString());
						if(rs != null){
							IncidentModel model = new IncidentModel();
							while(rs.next()){
								model.itsm_id            = rs.getString("INC_ID");
								model.product            = rs.getString("PRODUCT");
								model.title              = rs.getString("TITLE");
								model.impact_level       = rs.getString("IMPACT");
								model.outage_start       = rs.getString("OUTAGE START");
								model.outage_end         = rs.getString("OUTAGE END");
								model.area               = rs.getString("AREA");
								model.subarea            = rs.getString("SUB_AREA");
								model.description        = rs.getString("DESCRIPTION");
								model.solution           = rs.getString("SOLUTION");
								model.priority_code      = rs.getString("PRIORITY_CODE");
								model.open_time          = rs.getString("OPEN TIME");
								model.opened_by          = rs.getString("OPENED_BY");
								model.resolve_by_sdk     = rs.getString("RESOLVE_BY_SDK");
								model.update_time        = rs.getString("UPDATE TIME");
								model.update_action      = rs.getString("UPDATE_ACTION");
								model.updated_by         = rs.getString("UPDATED_BY");
								model.assignment         = rs.getString("ASSIGNMENT");
								model.assignee           = rs.getString("ASSIGNEE_NAME");
								model.caused_by_external = rs.getString("CAUSED_BY_EXTERNAL_SERVICE");
								model.caused_by_dept     = rs.getString("CAUSED_BY_DEPT");
								model.user_impacted      = rs.getString("USER_IMPACTED");
								model.sdk_detector       = rs.getString("SDK_INCIDENT_DETECTOR");
								model.created_by         = rs.getString("CREATED_BY");
								model.department         = rs.getString("DEPARTMENT");
								model.sdknote            = rs.getString("NOTE_SDK");
								model.customer_case      = rs.getString("CUSTOMER_IMPACTED");
								model.down_start         = rs.getString("DOWN_START");
								model.ccutime            = rs.getString("USER_CCUTIME");
								model.status             = rs.getString("INC_STATUS");
								model.internal_status    = rs.getString("INTERNAL_STATUS");
								model.rootcause          = rs.getString("ROOT_CAUSE");
								model.rootcause_category = rs.getString("ROOTCAUSE_CATEGORY");
								model.is_downtime        = rs.getString("IS_DOWNTIME");
								model.related_id_change  = rs.getString("RELATED_ID_CHANGE");
								model.related_id         = rs.getString("RELATED_ID");
								model.location           = rs.getString("LOCATION_FULL_NAME");
								model.reopened_by        = rs.getString("REOPENED_BY");
								model.reopen_time        = rs.getString("REOPEN TIME");
								
								SendIncidentSMS(model, mSMSNotifyTo, nSMSTimeoutMinutes, sdkDB);
								
								model.WriteToSDK();
							}
						}
						String s = "SynchFromITSM: Synchronized";
						System.out.println(s);
						MyLogger.WriteLog("INFO", s);
						MyLogger.WriteLog("INFO", query.toString());
					} else {
						String s = "SynchFromITSM: Waiting Count 0";
						System.out.println(s);
						MyLogger.WriteLog("INFO", s);
						MyLogger.WriteLog("INFO", count_query);
					}
				}
				String se_report_query = String.format("%s'%s'", "select distinct a.inc_id INC_ID from all_incident2010 a inner join sysattachmem1 at on(a.inc_id=at.topic) where a.impact<=2 AND lower(a.resolve_by_sdk)='se' AND (filename like '%incident report%' or filename like '%incident_report%') and a.sysmodtime >= ", synchFrom);
				// String se_report_query = String.format("%s", "select distinct a.inc_id INC_ID from all_incident2010 a inner join sysattachmem1 at on(a.inc_id=at.topic) where a.impact<=2 AND lower(a.resolve_by_sdk)='se' AND (filename like '%incident report%' or filename like '%incident_report%')");
				ResultSet rs_se_report = itsmDB.ExecuteQuery(se_report_query);
				if(rs_se_report != null){
					IncidentModel model = new IncidentModel();
					while(rs_se_report.next()){
						model.itsm_id = rs_se_report.getString("INC_ID");
						model.Update_SE_Report();
						model.Close_SE_Notification();
					}
					String s = "SynchFromITSM-SynchSE_Report: Synchronized";
					System.out.println(s);
					MyLogger.WriteLog("INFO", s);
					MyLogger.WriteLog("INFO", se_report_query);
				}
			} else {
				String s = "SynchFromITSM: Test ITSM connection fail";
				System.out.println(s);
				MyLogger.WriteLog("INFO", s);
			}
			itsmDB.CloseConnection();
		} catch(Exception e) {
			String s = "SynchFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
		
		// Specific synch
	}
	
	private boolean isSentIncidentSMS(IncidentModel oModel, MyMySQL sdkDB){
		boolean bResult = false;
		try{
			StringBuilder query = new StringBuilder();
			query.append("SELECT sms_notified_to FROM ");
			query.append(Config.MYSQL_TBL_INCIDENT);
			query.append(" WHERE itsm_incident_id='");
			query.append(oModel.itsm_id.concat("'"));
			
			ResultSet oRs = sdkDB.ExecuteQuery(query.toString());
			if(oRs.next()){
				String strSmsNotifiedTo = oRs.getString("sms_notified_to");
				if(strSmsNotifiedTo != null && !strSmsNotifiedTo.replace("|", "").isEmpty())
				{
					oModel.sms_notified_to = strSmsNotifiedTo;
					bResult = true;
				}
			}
		} catch(Exception e){
			String s = "SynchFromITSM CheckSentSMS Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
			
			bResult = true;
		}
		
		return bResult;
	}
	
	private int SendIncidentSMS(IncidentModel oModel, HashMap<String, HashMap<String, String>> arrIncidentOwnerInfo, int nSMSTimeoutMinutes, MyMySQL sdkDB){
		int nResult = Config.INCIDENT_SMS_RESULT_SKIP;
		try{
			oModel.sms_notified_to = null;
			StringBuilder strSentResult = new StringBuilder();
			
			if(oModel.department != null){
				String strDept = oModel.department.toLowerCase();
				if(!arrIncidentOwnerInfo.isEmpty() && strDept != null && arrIncidentOwnerInfo.containsKey(strDept)){
					if(IncidentLiveTime(oModel.open_time) <= nSMSTimeoutMinutes){
						if(!isSentIncidentSMS(oModel, sdkDB)){
							HashMap<String, String> mUserToSMS = arrIncidentOwnerInfo.get(strDept);
							
						    for(Map.Entry<String, String> oCursor:mUserToSMS.entrySet()) {
						    	String strUser = oCursor.getKey();
						    	String strIncidentLevelSubscribe = oCursor.getValue();
						    	
						    	StringBuilder strUserSent = new StringBuilder();
						    	strUserSent.append(strUser);
								if(strIncidentLevelSubscribe.indexOf(oModel.impact_level)>=0){
									StringBuilder query = new StringBuilder();
									query.append("SELECT mobile FROM ");
									query.append(Config.MYSQL_TBL_USER);
									query.append(" WHERE email like '");
									query.append(strUser.trim().concat("@").concat("%' LIMIT 1"));
			
									ResultSet oRsUser = sdkDB.ExecuteQuery(query.toString());
									if(oRsUser != null){
										if(oRsUser.next()){
											String strPhoneNumber = oRsUser.getString("mobile");
											strUserSent.append(":".concat(strPhoneNumber));
											if(strPhoneNumber != null && !strPhoneNumber.isEmpty())
											{
												String strSMSMsg = String.format("Incident L%s. %s:%s", oModel.impact_level, oModel.itsm_id, oModel.title.replace("[", "(").replace("]", ")"));
												String strSendResult = Utils.SendSMS(strPhoneNumber, strSMSMsg, Config.SMS_SERVICE_URL);
												// String strSendResult = "TEST";
												nResult = Config.INCIDENT_SMS_RESULT_OK;
												strUserSent.append(":".concat(strSendResult));
											} else {
												strUserSent.append(":PhoneNumber Not Found");
											}
										}
									} else {
										strUserSent.append(":User Not Found");
										nResult = Config.INCIDENT_SMS_RESULT_USER_NOT_FOUND;
									}
								} else {
									strUserSent.append(":Did not Subscribe");
									nResult = Config.INCIDENT_SMS_RESULT_NO_SUBSCRIBE;
								}
								strSentResult.append("|".concat(strUserSent.toString()));
							}
						} else {
							nResult = Config.INCIDENT_SMS_RESULT_ALREADY_SENT;
						}
					} else {
						nResult = Config.INCIDENT_SMS_RESULT_TIMEOUT;
					}
				}
			}
		
			String strIncidentSMSLog = String.format("Incident %s(%s) SMS Result: %s", oModel.itsm_id, oModel.department, nResult);
			if(nResult == Config.INCIDENT_SMS_RESULT_OK){
				strIncidentSMSLog.concat(String.format("Send sms to: %s", strSentResult.toString()));
				oModel.sms_notified_to = strSentResult.toString();
			}
			MyLogger.WriteLog("INFO", strIncidentSMSLog);
		
		} catch(Exception e){
			String s = "SynchFromITSM SendSMS Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
			
			nResult = Config.INCIDENT_SMS_RESULT_EXCEPTION;
		}
		
		return nResult;
	}
	
	private int IncidentLiveTime(String strITSMOpenTime){
    	int nResult = 0;
    	try{
	    	SimpleDateFormat formater=new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
	    	
	    	Calendar cal = Calendar.getInstance();
	    	cal.getTime();
	    	
	    	long nD1 = formater.parse(strITSMOpenTime).getTime();
	    	long nD2 = System.currentTimeMillis();
	    	
	    	long span = nD2 - nD1;
	    	nResult = (int) (span/1000/60);
    	} catch (Exception e){}
    	
    	return nResult;
    }
}