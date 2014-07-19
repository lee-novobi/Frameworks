package Watcher;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Model.AffectedCIModel;

import java.util.Date;
import java.text.DateFormat;
import java.text.SimpleDateFormat;

import java.sql.*;

public class SyncherAffectedCI {
	public void SynchFromITSM(){
		try{
		    String lastSynch     = Config.SynchFileGetProperty("AFCI_LAST_SYNCH");
		    String synchDistance = Config.SynchFileGetProperty("AFCI_SYNCH_DISTANCE");
		    String specificList  = Config.SynchFileGetProperty("AFCI_SYNCH_SPECIFIC");
		    String debugMode     = Config.SynchFileGetProperty("AFCI_DEBUG");
		    boolean run          = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_AFCI"));
		    
		    if(run){
			    SynchFromITSM(lastSynch, synchDistance, specificList, debugMode);
			    
			    DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("AFCI_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "AFCI Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "SyncherAFCI - SynchFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
	
	private void SynchFromITSM(String lastSynch, String synchDistance, String specificList, String debugMode){
		// General synch
		try{
			MyMSSQL itsmDB = new MyMSSQL();
			
			DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");						
		
			if(itsmDB.TestConnection()){
				StringBuilder query = new StringBuilder();
				query.append("SELECT * ");
				query.append(String.format("FROM %s ", Config.MSSQL_TBL_AFFECTED_CI));

				ResultSet rs = itsmDB.ExecuteQuery(query.toString());
				if(rs != null){
					AffectedCIModel model = new AffectedCIModel();
					model.Truncate();
					while(rs.next()){
						model.logical_name      = rs.getString("LOGICAL_NAME");
						model.relationship_name = rs.getString("RELATIONSHIP_NAME");
						model.record_number     = rs.getInt("RECORD_NUMBER");
						model.related_ci        = rs.getString("RELATED_CIS");
						model.created_date      = formatter.format(new Date());
						
						model.WriteToSDK();
					}
				}
				String s = "SyncherAFCI - SynchFromITSM: Synchronized";
				System.out.println(s);
				MyLogger.WriteLog("INFO", s);
				MyLogger.WriteLog("INFO", query.toString());
			} else {
				String s = "SyncherAFCI - SynchFromITSM: Test ITSM connection fail";
				System.out.println(s);
				MyLogger.WriteLog("INFO", s);
			}
			itsmDB.CloseConnection();
		} catch(Exception e) {
			String s = "SyncherAFCI - SynchFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
		
		// Specific synch
	}
}