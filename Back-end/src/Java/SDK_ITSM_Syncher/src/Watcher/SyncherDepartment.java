package Watcher;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Helper.MyMySQL;
import Model.IncidentModel;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import java.sql.*;
public class SyncherDepartment {
	public void GetDepartmentListFromITSM(){
		MyMSSQL itsmDB = new MyMSSQL();
		MyMySQL sdkDB  = new MyMySQL();
		
		try{
			boolean run = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_DEPARTMENT"));
		    
		    if(run){
				if(itsmDB.TestConnection()){
					String query = String.format("SELECT DISTINCT TOP %s %s as department FROM %s ORDER BY %s",
							Config.MSSQL_SELECT_LIMIT,
							Config.MSSQL_FIELD_PRODUCT_DEPARTMENT_NAME,
							Config.MSSQL_TBL_PRODUCT_M1,
							Config.MSSQL_FIELD_PRODUCT_DEPARTMENT_NAME);
					
					ResultSet rs = itsmDB.ExecuteQuery(query);
					if(rs != null){
						if(sdkDB.TestConnection()){
							Connection conn = sdkDB.GetConnection();
							
							query = "UPDATE `department` SET deleted='1' WHERE is_itsm_department=1";
							conn.createStatement().execute(query);
							
							while(rs.next()){
								try{
									String department_name = rs.getString("department");
									if(department_name != null){
										query = String.format("SELECT %s AS id FROM department WHERE is_itsm_department=1 AND `name` LIKE ? LIMIT 1", Config.MYSQL_FIELD_ID_OF_TBL_DEPARTMENT);
										PreparedStatement stment = conn.prepareStatement(query);
										stment.setString(1, department_name);
										
										ResultSet rsFind = stment.executeQuery();
										if(rsFind != null)
						                {
						                    if(rsFind.next()){
						                    	int nIDFound = rsFind.getInt("id");
						                    	query = String.format("UPDATE `department` SET deleted='0' WHERE %s=%s", Config.MYSQL_FIELD_ID_OF_TBL_DEPARTMENT, String.valueOf(nIDFound));
												conn.createStatement().execute(query);
						                    } else {
						                    	query = "INSERT INTO department (name,is_itsm_department) VALUES(?,1)";
												
						                    	PreparedStatement stment1 = conn.prepareStatement(query);
						                    	stment1.setString(1, department_name);
						                    	stment1.execute();
						                    	stment1.close();
						                    }
						                }
										stment.close();
									}
								} catch(Exception e){
									String s = "GetDepartmentListFromITSM Exception: " + e.getMessage();
									System.err.println(s);
									MyLogger.WriteLog("EXCEPTION", s);
								}
							}
							
							String s = "Departmnet Syncher: Synchronized";
							System.out.println(s);
							MyLogger.WriteLog("INFO", s);
							MyLogger.WriteLog("INFO", query.toString());
							// sdkDB.CloseConnection();
						}
					}
				}
				itsmDB.CloseConnection();
				
				DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			    Config.SynchFileSetProperty("DEPARTMENT_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "Departmnet Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "GetDepartmentListFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
}