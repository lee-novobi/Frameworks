package Watcher;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Helper.MyMySQL;

public class SyncherAssignee {
	public void GetAssigneeListFromITSM(){
		MyMSSQL itsmDB = new MyMSSQL();
		MyMySQL sdkDB  = new MyMySQL();
		
		try{
			boolean run = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_ASSIGNEE"));
		    
		    if(run){
				if(itsmDB.TestConnection()){
					String query = String.format("SELECT TOP %s %s as assignment,%s as assignee FROM %s ORDER BY %s",
							Config.MSSQL_SELECT_LIMIT,
							Config.MSSQL_FIELD_ASSIGNMENT_NAME,
							Config.MSSQL_FIELD_ASSIGNEE_NAME,
							Config.MSSQL_TBL_ASSIGNEE,
							Config.MSSQL_FIELD_ASSIGNMENT_NAME);
					
					ResultSet rs = itsmDB.ExecuteQuery(query);
					if(rs != null){
						if(sdkDB.TestConnection()){
							Connection conn = sdkDB.GetConnection();
							
							query = "TRUNCATE `assignee`";
							
							conn.createStatement().execute(query);
							
							while(rs.next()){
								String assignment_group_name = rs.getString("assignment");
								String assignee_name         = rs.getString("assignee");
								
								query = "INSERT INTO assignee (name,assignment_group) VALUES(?,?)";
															
								PreparedStatement stment = conn.prepareStatement(query);
								
								stment.setString(1, assignee_name);
								stment.setString(2, assignment_group_name);
								
								stment.execute();
							}
							
							String s = "Assignee Syncher: Synchronized";
							System.out.println(s);
							MyLogger.WriteLog("INFO", s);
							MyLogger.WriteLog("INFO", query.toString());
							// sdkDB.CloseConnection();
						}
					}
				}
				itsmDB.CloseConnection();
				
				DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("ASSIGNEE_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "Assignee Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "GetAssigneeListFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
	}
}
