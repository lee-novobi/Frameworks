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

public class SyncherAssigmnentGroup {
	public void GetAssignmentGroupListFromITSM(){
		MyMSSQL itsmDB = new MyMSSQL();
		MyMySQL sdkDB  = new MyMySQL();
		
		try{
			boolean run = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_ASSIGNMENT_GROUP"));
		    
		    if(run){
				if(itsmDB.TestConnection()){
					String query = String.format("SELECT TOP %s %s AS assignment,%s AS product,%s AS department FROM %s ORDER BY %s",
							Config.MSSQL_SELECT_LIMIT,
							Config.MSSQL_FIELD_ASSIGNMENT_NAME,
							Config.MSSQL_FIELD_ASSIGNMENT_PRODUCT,
							Config.MSSQL_FIELD_ASSIGNMENT_DEPARTMENT,
							Config.MSSQL_TBL_ASSIGNMENT,
							Config.MSSQL_FIELD_ASSIGNMENT_NAME);
					
					ResultSet rs = itsmDB.ExecuteQuery(query);
					if(rs != null){
						if(sdkDB.TestConnection()){
							Connection conn = sdkDB.GetConnection();
							
							query = "TRUNCATE `assignment_group`";
							
							conn.createStatement().execute(query);
							
							while(rs.next()){
								String assignment_group_name = rs.getString("assignment");
								String assignment_group_product = rs.getString("product");
								String assignment_group_department = rs.getString("department");
								
								query = "INSERT INTO assignment_group (name, product, department) VALUES(?,?,?)";
															
								PreparedStatement stment = conn.prepareStatement(query);
								
								stment.setString(1, assignment_group_name);
								stment.setString(2, assignment_group_product);
								stment.setString(3, assignment_group_department);
								
								stment.execute();
							}
							
							String s = "AssignmentGroup Syncher: Synchronized";
							System.out.println(s);
							MyLogger.WriteLog("INFO", s);
							MyLogger.WriteLog("INFO", query.toString());
							// sdkDB.CloseConnection();
						}
					}
				}
				itsmDB.CloseConnection();
				
				DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("ASSIGNMENT_GROUP_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "AssignmentGroup Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "GetAssignmentGroupListFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
}
