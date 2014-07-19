package Watcher;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMSSQL;
import Helper.MyMySQL;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import java.sql.*;

public class SyncherProduct {
	public void GetProductListFromITSM(){
		MyMSSQL itsmDB = new MyMSSQL();
		MyMySQL sdkDB  = new MyMySQL();
		
		try{
			boolean run = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_PRODUCT"));
		    if(run){
				if(itsmDB.TestConnection()){
					String query = String.format(
												/*01*/"SELECT m1.%s AS product," +
												/*02*/"m1.%s AS department," +
												/*03*/"m1.%s AS ob_date," +
												/*04*/"m1.%s AS assignment_group_l1," +
												/*05*/"m1.%s AS itsm_id," +
												/*06*/"m1.%s AS [type],m2.%s AS platform_type," +
												/*07*/"m1.%s AS subtype," +
												/*08*/"m1.%s AS comments,m2.%s AS product_code " +
												/*09*/"FROM %s m1 " +
												/*10*/"LEFT JOIN %s m2 " +
												/*11*/"ON(m1.%s=" +
												/*12*/"m2.%s) " +
												/*13*/"WHERE %s='%s' " +
												/*14*/"ORDER BY m1.%s",
							/*01*/Config.MSSQL_FIELD_PRODUCT_NAME,
							/*02*/Config.MSSQL_FIELD_PRODUCT_DEPARTMENT_NAME,
							/*03*/Config.MSSQL_FIELD_PRODUCT_OB_DATE,
							/*04*/Config.MSSQL_FIELD_PRODUCT_ASSIGNMENT_GROUP_L1,
							/*05*/Config.MSSQL_FIELD_PRODUCT_ITSM_CODE,
							/*06*/Config.MSSQL_FIELD_PRODUCT_TYPE, Config.MSSQL_FIELD_PRODUCT_PLATFORM_TYPE,
							/*07*/Config.MSSQL_FIELD_PRODUCT_SUBTYPE,
							/*08*/Config.MSSQL_FIELD_PRODUCT_COMMENTS, Config.MSSQL_FIELD_PRODUCT_CODE,
							/*09*/Config.MSSQL_TBL_PRODUCT_M1,
							/*10*/Config.MSSQL_TBL_PRODUCT_M2,
							/*11*/Config.MSSQL_FIELD_PRODUCT_NAME,
							/*12*/Config.MSSQL_FIELD_PRODUCT_NAME,
							/*13*/Config.MSSQL_FIELD_PRODUCT_ISTATUS, Config.MSSQL_FIELD_PRODUCT_ISTATUS_VALUE_INUSE,
							/*14*/Config.MSSQL_FIELD_PRODUCT_NAME);
					
					ResultSet rs = itsmDB.ExecuteQuery(query);
					if(rs != null){
						if(sdkDB.TestConnection()){
							Connection conn = sdkDB.GetConnection();
							
							query = String.format("UPDATE %s SET deleted='1' WHERE is_itsm_product='1'", Config.MYSQL_TBL_PRODUCT);
							conn.createStatement().execute(query);
							
							while(rs.next()){
								try{
									String product_name          = rs.getString("product");
									String department_name       = rs.getString("department");
									String ob_date               = rs.getString("ob_date");
									String assignment_group      = rs.getString("assignment_group_l1");
									String itsm_id               = rs.getString("itsm_id");
									String product_type          = rs.getString("type");
									String product_platform_type = rs.getString("platform_type");
									String subtype               = rs.getString("subtype");
									String comments              = rs.getString("comments");
									String product_code          = rs.getString("product_code");
									
									int nSDKDepartmentID  = 0;
									if(product_name != null){
										// Find Department in SDK DB
										query = String.format("SELECT %s AS id FROM %s WHERE is_itsm_department=1 AND `name` LIKE ? LIMIT 1", Config.MYSQL_FIELD_ID_OF_TBL_DEPARTMENT, Config.MYSQL_TBL_DEPARTMENT);
										PreparedStatement stment = conn.prepareStatement(query);
										stment.setString(1, department_name);
										ResultSet rsFind = stment.executeQuery();
										if(rsFind != null && rsFind.next()){
											nSDKDepartmentID = rsFind.getInt("id");
										}
										
										query = String.format("SELECT %s AS id FROM %s WHERE is_itsm_product=1 AND `name` LIKE ? LIMIT 1", Config.MYSQL_FIELD_ID_OF_TBL_PRODUCT, Config.MYSQL_TBL_PRODUCT);
										stment = conn.prepareStatement(query);
										stment.setString(1, product_name);
										
										rsFind = stment.executeQuery();
										if(rsFind != null)
						                {
						                    if(rsFind.next()){
						                    	int nIDFound = rsFind.getInt("id");
						                    	query = String.format("UPDATE %s SET deleted='0'," +
						                    			/*01*/"department_name=?," +
						                    			/*02*/"ob_date=?," +
						                    			/*03*/"assignment_group_l1=?," +
						                    			/*04*/"itsm_id=?," +
						                    			/*05*/"itsm_type=?," +
						                    			/*06*/"itsm_platform_type=?," +
						                    			/*07*/"itsm_subtype=?," +
						                    			/*08*/"itsm_comments=?," +
						                    			/*09*/"product_code=?," +
						                    			"department_id=%s WHERE %s=%s", Config.MYSQL_TBL_PRODUCT, String.valueOf(nSDKDepartmentID), Config.MYSQL_FIELD_ID_OF_TBL_PRODUCT, String.valueOf(nIDFound));
						                    	PreparedStatement stment1 = conn.prepareStatement(query);
						                    	stment1.setString(1, department_name);
						                    	stment1.setString(2, ob_date);
						                    	stment1.setString(3, assignment_group);
						                    	stment1.setString(4, itsm_id);
						                    	stment1.setString(5, product_type);
						                    	stment1.setString(6, product_platform_type);
						                    	stment1.setString(7, subtype);
						                    	stment1.setString(8, comments);
						                    	stment1.setString(9, product_code);
						                    	stment1.execute();
						                    	stment1.close();
						                    } else {
						                    	query = String.format("INSERT INTO %s (" +
						                    			/*01*/"name," +
						                    			/*02*/"department_name," +
						                    			/*03*/"ob_date," +
						                    			/*04*/"assignment_group_l1," +
						                    			/*05*/"itsm_id," +
						                    			/*06*/"itsm_type," +
						                    			/*07*/"itsm_platform_type," +
						                    			/*08*/"itsm_subtype," +
						                    			/*09*/"itsm_comments," +
						                    			/*10*/"product_code," +
						                    			/*11*/"is_itsm_product," +
						                    			/*12*/"department_id" +
						                    			") VALUES(?,?,?,?,?,?,?,?,?,?,?,?)", Config.MYSQL_TBL_PRODUCT);
						                    	PreparedStatement stment1 = conn.prepareStatement(query);
						                    	stment1.setString(1, product_name);
						                    	stment1.setString(2, department_name);
						                    	stment1.setString(3, ob_date);
						                    	stment1.setString(4, assignment_group);
						                    	stment1.setString(5, itsm_id);
						                    	stment1.setString(6, product_type);
						                    	stment1.setString(7, product_platform_type);
						                    	stment1.setString(8, subtype);
						                    	stment1.setString(9, comments);
						                    	stment1.setString(10, product_code);
						                    	stment1.setInt(11, 1);
						                    	stment1.setInt(12, nSDKDepartmentID);
						                    	stment1.execute();
						                    	stment1.close();
						                    }
						                }
										stment.close();
									}
								} catch(Exception e){
									String s = "GetProductListFromITSM Exception: " + e.getMessage();
									System.err.println(s);
									MyLogger.WriteLog("EXCEPTION", s);
								}
							}
							
							String s = "Product Syncher: Synchronized";
							System.out.println(s);
							MyLogger.WriteLog("INFO", s);
							MyLogger.WriteLog("INFO", query.toString());
							// sdkDB.CloseConnection();
						}
					}
				}
				itsmDB.CloseConnection();
				
				DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
				Config.SynchFileSetProperty("PRODUCT_LAST_SYNCH", dtfm.format(new Date()));
		    } else {
		    	String s = "Product Syncher: OFF";
				System.err.println(s);
				MyLogger.WriteLog("INFO", s);
		    }
		} catch(Exception e) {
			String s = "GetProductListFromITSM Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
				
	}
}
