package Watcher;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Model.TaskModel;

import java.util.Calendar;
import java.util.Date;
import java.text.DateFormat;
import java.text.SimpleDateFormat;

import java.sql.*;

public class SyncherTask {
	public void SynchFromITSM(){
		try{
		    String lastSynch     = Config.SynchFileGetProperty("TASK_LAST_SYNCH");
		    String synchDistance = Config.SynchFileGetProperty("TASK_SYNCH_DISTANCE");
		    String specificList  = Config.SynchFileGetProperty("TASK_SYNCH_SPECIFIC");
		    String debugMode     = Config.SynchFileGetProperty("TASK_DEBUG");
		    boolean run          = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_TASK"));
		    
		    if(run){
			    SynchFromITSM(lastSynch, synchDistance, specificList, debugMode);
			    
			    DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("TASK_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "Task Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "SyncherTask - SynchFromITSM_1 Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
	
	private void SynchFromITSM(String lastSynch, String synchDistance, String specificList, String debugMode){
		// General synch
		try{
			MyMSSQL itsmDB = new MyMSSQL();
			
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
			
			String synchFrom = formatter.format(c1.getTime());
			
			if(itsmDB.TestConnection()){
				String count_query = String.format("SELECT COUNT(1) AS waiting_count FROM %s WHERE [SYSMODTIME]>='%s'",
						Config.MSSQL_TBL_TASK_2, synchFrom);
				
				ResultSet rs = itsmDB.ExecuteQuery(count_query);
				if(rs != null){
					rs.next();
					int waiting_count = rs.getInt("waiting_count");
					
					if(waiting_count > 0){
						StringBuilder query = new StringBuilder();
						query.append("SELECT T1.[NUMBER],");
						query.append("[CATEGORY],[STATUS],[REQUESTED_BY],[ASSIGNED_TO],[ASSIGN_DEPT],[PLANNED_START],[PLANNED_END],");
						query.append("[CURRENT_PHASE],[DATE_ENTERED],[PARENT_CHANGE],[CLOSE_TIME],[DESCRIPTION],[BRIEF_DESC],[DOWN_START],");
						query.append("[DOWN_END],[ACTUALSTART],[ACTUALEND],[SYSMODTIME] ");
						query.append(String.format("FROM %s T1 INNER JOIN %s T2 ON(T1.[NUMBER]=T2.[NUMBER]) WHERE [SYSMODTIME]>='%s' AND ASSIGN_DEPT LIKE 'SDK_%s'",
								Config.MSSQL_TBL_TASK_1, Config.MSSQL_TBL_TASK_2, synchFrom, "%"));
						System.out.println(query.toString());
						rs = itsmDB.ExecuteQuery(query.toString());
						if(rs != null){
							TaskModel model = new TaskModel();
							while(rs.next()){
								model.itsm_id       = rs.getString("NUMBER");
								model.category      = rs.getString("CATEGORY");
								model.status        = rs.getString("STATUS");
								model.requested_by  = rs.getString("REQUESTED_BY");
								model.assigned_to   = rs.getString("ASSIGNED_TO");
								model.assigned_dept = rs.getString("ASSIGN_DEPT");
								model.planned_start = rs.getString("PLANNED_START");
								model.planned_end   = rs.getString("PLANNED_END");
								model.current_phase = rs.getString("CURRENT_PHASE");
								model.date_entered  = rs.getString("DATE_ENTERED");
								model.parent_change = rs.getString("PARENT_CHANGE");
								model.description   = rs.getString("DESCRIPTION");
								model.brief_desc    = rs.getString("BRIEF_DESC");
								model.down_start    = rs.getString("DOWN_START");
								model.down_end      = rs.getString("DOWN_END");
								model.actual_start  = rs.getString("ACTUALSTART");
								model.actual_end    = rs.getString("ACTUALEND");
								model.last_modify   = rs.getString("SYSMODTIME");
								model.close_time    = rs.getString("CLOSE_TIME");
								model.created_date  = formatter.format(new Date());
								
								model.WriteToSDK();
							}
						}
						String s = "SyncherTask - SynchFromITSM: Synchronized";
						System.out.println(s);
						MyLogger.WriteLog("INFO", s);
						MyLogger.WriteLog("INFO", query.toString());
					} else {
						String s = "SyncherTask - SynchFromITSM: Waiting Count 0";
						System.out.println(s);
						MyLogger.WriteLog("INFO", s);
						MyLogger.WriteLog("INFO", count_query);
					}
				}
			} else {
				String s = "SyncherTask - SynchFromITSM: Test ITSM connection fail";
				System.out.println(s);
				MyLogger.WriteLog("INFO", s);
			}
			itsmDB.CloseConnection();
		} catch(Exception e) {
			String s = "SyncherTask - SynchFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
		
		// Specific synch
	}
}