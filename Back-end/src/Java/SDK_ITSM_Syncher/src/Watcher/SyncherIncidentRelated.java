package Watcher;

import java.sql.ResultSet;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Model.IncidentRelatedModel;

public class SyncherIncidentRelated {
	public void SynchFromITSM(){
		try{
		    String lastSynch     = Config.SynchFileGetProperty("INCIDENT_RELATED_LAST_SYNCH");
		    String synchDistance = Config.SynchFileGetProperty("INCIDENT_RELATED_SYNCH_DISTANCE");
		    String specificList  = Config.SynchFileGetProperty("INCIDENT_RELATED_SYNCH_SPECIFIC");
		    String debugMode     = Config.SynchFileGetProperty("INCIDENT_RELATED_DEBUG");
		    boolean run          = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_INCIDENT_RELATED"));
		    
		    if(run){
			    SynchFromITSM(lastSynch, synchDistance, specificList, debugMode);
			    
			    DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("INCIDENT_RELATED_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "Incident Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "GetProductListFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
	
	public void SynchFromITSM(String lastSynch, String synchDistance, String specificList, String debugMode){
		MyMSSQL itsmDB = new MyMSSQL();
		try{
			boolean run = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_INCIDENT_RELATED"));
		    
		    if(run){
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
					String count_query = String.format("SELECT COUNT(1) AS waiting_count FROM %s WHERE ([SYSMODTIME]>='%s')",
							Config.MSSQL_TBL_INCIDENT_RELATED, synchFrom);
					
					ResultSet rs = itsmDB.ExecuteQuery(count_query);
					if(rs != null){
						rs.next();
						int waiting_count = rs.getInt("waiting_count");
						
						if(waiting_count > 0){
							StringBuilder query = new StringBuilder();
							query.append("SELECT [SOURCE],[SOURCE_FILENAME],[DEPEND],[DEPEND_FILENAME],[TYPE],[SOURCE_ACTIVE],");
							query.append("[DEPEND_ACTIVE],[SYSMODCOUNT],[SYSMODUSER],[SYSMODTIME],[DESC],[CARTITEMID] ");
							query.append(String.format("FROM %s WHERE [SYSMODTIME] >= '%s'",
									Config.MSSQL_TBL_INCIDENT_RELATED,synchFrom));
							
							rs = itsmDB.ExecuteQuery(query.toString());
							if(rs != null){
								IncidentRelatedModel model = new IncidentRelatedModel();
								while(rs.next()){
									model.source            = rs.getString("SOURCE");
									model.source_filename   = rs.getString("SOURCE_FILENAME");
									model.depend            = rs.getString("DEPEND");
									model.depend_filename   = rs.getString("DEPEND_FILENAME");
									model.type       		= rs.getString("TYPE");
									model.source_active     = rs.getString("SOURCE_ACTIVE");
									model.depend_active     = rs.getString("DEPEND_ACTIVE");
									model.sysmodcount       = rs.getString("SYSMODCOUNT");
									model.sysmoduser        = rs.getString("SYSMODUSER");
									model.sysmodtime        = rs.getString("SYSMODTIME");
									model.desc      		= rs.getString("DESC");
									model.cartimeid         = rs.getString("CARTITEMID");
									
									model.WriteToSDK();
								}
							}
							String s = "SyncherIncidentRelated - SynchFromITSM: Synchronized";
							System.out.println(s);
							MyLogger.WriteLog("INFO", s);
							MyLogger.WriteLog("INFO", query.toString());
						} else {
							String s = "SyncherIncidentRelated - SynchFromITSM: Waiting Count 0";
							System.out.println(s);
							MyLogger.WriteLog("INFO", s);
							MyLogger.WriteLog("INFO", count_query);
						}
					}
				} else {
					String s = "SyncherIncidentRelated - SynchFromITSM: Test ITSM connection fail";
					System.out.println(s);
					MyLogger.WriteLog("INFO", s);
				}
				itsmDB.CloseConnection();
		    }
		    
		} catch(Exception e) {
			String s = "SyncherIncidentRelated - SynchFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
	}
}
