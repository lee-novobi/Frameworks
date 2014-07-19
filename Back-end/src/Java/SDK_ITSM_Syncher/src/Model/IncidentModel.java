package Model;

import Helper.*;

import java.io.PrintStream;
import java.sql.*;

public class IncidentModel
{

    public String itsm_id;
    public String follow_shift_id;
    public String title;
    public String created_by;
    public String created_date;
    public String status;
    public String outage_start;
    public String outage_end;
    public String impact_level;
    public String description;
    public String department;
    public String product;
    public String user_impacted;
    public String customer_case;
    public String assignment;
    public String assignee;
    public String created_user;
    public String caused_by_external;
    public String caused_by_dept;
    public String area;
    public String subarea;
    public String sdknote;
    public String ccutime;
    public String solution;
    public String priority_code;
    public String open_time;
    public String opened_by;
    public String resolve_by_sdk;
    public String update_time;
    public String update_action;
    public String updated_by;
    public String sdk_detector;
    public String down_start;
    public String internal_status;
    public String rootcause;
    public String rootcause_category;
    public String is_downtime;
    public String related_id_change;
    public String related_id;
    public String location;
    public String reopened_by;
    public String reopen_time;
    public String sms_notified_to;
    public String source_from;
    public String rejected_reason;
    
    public IncidentModel incident;

    public IncidentModel()
    {
    	this.incident = null;
    }

    public void WriteToSDK()
    {
        if(itsm_id != null && itsm_id.length() > 0)
        {
            if(IsExistedInSDK())
            {
                UpdateToSDK();
            } else
            {
                InsertToSDK();
            }
        }
    }

    public boolean IsExistedInSDK()
    {
        boolean result = false;
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                String count_query = String.format("SELECT internal_status,status,source_from FROM %s WHERE itsm_incident_id='%s'", new Object[] {
                    Config.MYSQL_TBL_INCIDENT, itsm_id
                });
                ResultSet rs = sdkDB.ExecuteQuery(count_query);
                if(rs != null)
                {
                    if(rs.next()){
                    	this.incident = new IncidentModel();
                    	this.incident.itsm_id = itsm_id;
                    	this.incident.internal_status = rs.getString("internal_status");
                    	this.incident.status = rs.getString("status");
                    	this.incident.source_from = rs.getString("source_from");
                    	if(this.incident.internal_status == null){
                    		this.incident.internal_status = "";
                    	}
                    	if(this.incident.status == null){
                    		this.incident.status = "";
                    	}
                    	if(this.incident.source_from == null){
                    		this.incident.source_from = "";
                    	}
                    	result = true;
                    }
                }
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("INCIDENT MODEL: IsExistedInSDK Exception: ")).append(e.getMessage()).toString();
            System.out.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
        return result;
    }

    public void InsertToSDK()
    {
        StringBuilder query = new StringBuilder();
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                /*--*/query.append("INSERT INTO ");
                /*--*/query.append(Config.MYSQL_TBL_INCIDENT);
                /*--*/query.append(" (");
                /*01*/query.append("title,");
                /*02*/query.append("created_by,");
                /*03*/query.append("created_date,");
                /*04*/query.append("status,");
                /*05*/query.append("outage_start,");
                /*06*/query.append("outage_end,");
                /*07*/query.append("impact_level,");
                /*08*/query.append("description,");
                /*09*/query.append("department,");
                /*10*/query.append("product,");
                /*11*/query.append("user_impacted,");
                /*12*/query.append("customer_case,");
                /*13*/query.append("assignment,");
                /*14*/query.append("assignee,");
                /*15*/query.append("created_user,");
                /*16*/query.append("caused_by_external,");
                /*17*/query.append("area,");
                /*18*/query.append("subarea,");
                /*19*/query.append("sdknote,");
                /*20*/query.append("ccutime,");
                /*21*/query.append("internal_status,");
                /*22*/query.append("follow_shift_id,");
                /*23*/query.append("caused_by_external_dept,");
                /*24*/query.append("resolved_by,");
                /*25*/query.append("detector,");
                /*26*/query.append("downtime_start,");
                /*27*/query.append("itsm_open_time,");
                /*28*/query.append("itsm_incident_id,");
                /*29*/query.append("rootcause,");
                /*30*/query.append("rootcause_category,");
                /*31*/query.append("is_downtime,");
                /*32*/query.append("related_id_change,");
                /*33*/query.append("related_id,");
                /*34*/query.append("location,");
                /*35*/query.append("itsm_last_update_time,");
                /*36*/query.append("updated_by,");
                /*37*/query.append("sms_notified_to,");
                /*38*/query.append("solution,");
                /*39*/query.append("update_action)");
                /*--*/query.append(" VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, title);
                stament.setString(2, created_by);
                stament.setString(3, Utils.GetShiftDate());
                stament.setString(4, status);
                stament.setString(5, outage_start);
                stament.setString(6, outage_end);
                stament.setString(7, impact_level);
                stament.setString(8, description);
                stament.setString(9, department);
                stament.setString(10, product);
                stament.setString(11, user_impacted);
                stament.setString(12, customer_case);
                stament.setString(13, assignment);
                stament.setString(14, assignee);
                stament.setString(15, created_by);
                stament.setString(16, caused_by_external);
                stament.setString(17, area);
                stament.setString(18, subarea);
                stament.setString(19, sdknote);
                stament.setString(20, ccutime);
                stament.setString(21, internal_status);
                stament.setInt(22, Utils.GetShiftID());
                stament.setString(23, caused_by_dept);
                stament.setString(24, resolve_by_sdk);
                stament.setString(25, sdk_detector);
                stament.setString(26, down_start);
                stament.setString(27, open_time);
                stament.setString(28, itsm_id);
                stament.setString(29, rootcause);
                stament.setString(30, rootcause_category);
                stament.setString(31, is_downtime);
                stament.setString(32, related_id_change);
                stament.setString(33, related_id);
                stament.setString(34, location);
                stament.setString(35, update_time);
                stament.setString(36, updated_by);
                stament.setString(37, sms_notified_to);
                stament.setString(38, solution);
                stament.setString(39, update_action);
                stament.execute();
                stament.close();
                
                query = new StringBuilder();
                /*--*/query.append("INSERT INTO ");
                /*--*/query.append(Config.MYSQL_TBL_INCIDENT_HISTORY);
                query.append(" (incident_id,handle_shift_id,handle_shift_date,created_date)");
                query.append(" SELECT ifl.id, f_get_current_shift(), f_get_current_shift_date(), NOW() FROM ");
                query.append(Config.MYSQL_TBL_INCIDENT);
                query.append(" ifl LEFT JOIN ");
                query.append(Config.MYSQL_TBL_INCIDENT_HISTORY);
                query.append(" ih ON(ih.incident_id=ifl.id)");
                query.append(" WHERE ih.id IS NULL AND itsm_incident_id='");
                query.append(itsm_id);
                query.append("'");
                
                stament = conn.prepareStatement(query.toString());
                stament.execute();
                stament.close();
                
                // Kiem tra Incident co vua duoc close hay khong
                // Neu vua close thi close boi SE hay SDK ?
                if(this.internal_status.equalsIgnoreCase("closed"))
                {
                	this.UpdateClosedTime();
                	
                	boolean isClosedBy247 = Test247User(updated_by);
                	if(!isClosedBy247){
                		SetCloseBySE();
                	}
                }
                
                String s = (new StringBuilder("InsertToSDK: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("INCIDENT MODEL: InsertToSDK Exception: ")).append(e.getMessage()).toString();
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("INFO", q);
        }
    }

    public void UpdateToSDK()
    {
        StringBuilder query = new StringBuilder();
        PreparedStatement stament = null;
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                /*--*/query.append("UPDATE ");
                /*--*/query.append(Config.MYSQL_TBL_INCIDENT);
                /*--*/query.append(" SET ");
                /*01*/query.append("title=?,");
                /*02*/query.append("created_by=?,");
                /*03*/query.append("status=?,");
                /*04*/query.append("outage_start=?,");
                /*05*/query.append("outage_end=?,");
                /*06*/query.append("impact_level=?,");
                /*07*/query.append("description=?,");
                /*08*/query.append("department=?,");
                /*09*/query.append("product=?,");
                /*10*/query.append("user_impacted=?,");
                /*11*/query.append("customer_case=?,");
                /*12*/query.append("assignment=?,");
                /*13*/query.append("assignee=?,");
                /*14*/query.append("created_user=?,");
                /*15*/query.append("caused_by_external=?,");
                /*16*/query.append("area=?,");
                /*17*/query.append("subarea=?,");
                /*18*/query.append("sdknote=?,");
                /*19*/query.append("ccutime=?,");
                /*20*/query.append("caused_by_external_dept=?,");
                /*21*/query.append("resolved_by=?,");
                /*22*/query.append("detector=?,");
                /*23*/query.append("downtime_start=?,");
                /*24*/query.append("itsm_open_time=?,");
                /*25*/query.append("rootcause=?,");
                /*26*/query.append("rootcause_category=?,");
                /*27*/query.append("internal_status=?,");
                /*28*/query.append("is_downtime=?,");
                /*29*/query.append("related_id_change=?,");
                /*30*/query.append("related_id=?,");
                /*31*/query.append("location=?,");
                /*32*/query.append("itsm_last_update_time=?,");
                /*33*/query.append("updated_by=?,");
                /*34*/query.append("reopen_time=?,");
                /*35*/query.append("solution=?,");
                /*36*/query.append("update_action=?,");
                if(sms_notified_to != null){
                /*37*/	query.append("reopened_by=?,");
                /*38*/	query.append("sms_notified_to=? ");
                /*39*/	query.append("WHERE itsm_incident_id=?");
                } else {
            	/*37*/	query.append("reopened_by=? ");
                /*38*/	query.append("WHERE itsm_incident_id=?");
                }
                Connection conn = sdkDB.GetConnection();
                stament = conn.prepareStatement(query.toString());
                stament.setString(1, title);
                stament.setString(2, created_by);
                stament.setString(3, status);
                stament.setString(4, outage_start);
                stament.setString(5, outage_end);
                stament.setString(6, impact_level);
                stament.setString(7, description);
                stament.setString(8, department);
                stament.setString(9, product);
                stament.setString(10, user_impacted);
                stament.setString(11, customer_case);
                stament.setString(12, assignment);
                stament.setString(13, assignee);
                stament.setString(14, created_by);
                stament.setString(15, caused_by_external);
                stament.setString(16, area);
                stament.setString(17, subarea);
                stament.setString(18, sdknote);
                stament.setString(19, ccutime);
                stament.setString(20, caused_by_dept);
                stament.setString(21, resolve_by_sdk);
                stament.setString(22, sdk_detector);
                stament.setString(23, down_start);
                stament.setString(24, open_time);
                stament.setString(25, rootcause);
                stament.setString(26, rootcause_category);
                stament.setString(27, internal_status);
                stament.setString(28, is_downtime);
                stament.setString(29, related_id_change);
                stament.setString(30, related_id);
                stament.setString(31, location);
                stament.setString(32, update_time);
                stament.setString(33, updated_by);
                stament.setString(34, reopen_time);
                stament.setString(35, solution);
                stament.setString(36, update_action);
                stament.setString(37, reopened_by);
                if(sms_notified_to != null){
                	stament.setString(38, sms_notified_to);
                	stament.setString(39, itsm_id);
                } else {
                	stament.setString(38, itsm_id);
                }
                stament.execute();
                stament.close();
                
                // Kiem tra Incident co vua duoc reject hay khong
                // Neu vua reject thi lay Update Reason moi nhat lam Reject Reason ?
                if(this.incident != null & this.incident.source_from.equalsIgnoreCase(Config.SOURCE_FROM_CS)
                	&& (!this.incident.status.equalsIgnoreCase("rejected") && this.status.equalsIgnoreCase("rejected")))
                {
                	this.UpdateRejectedReason();
                }
                
                // Kiem tra Incident co vua duoc close hay khong
                // Neu vua close thi close boi SE hay SDK ?
                if(this.incident != null
                	&& (this.incident.internal_status == null || !this.incident.internal_status.equalsIgnoreCase("closed"))
                	&& (this.internal_status != null && this.internal_status.equalsIgnoreCase("closed")))
                {
                	this.UpdateClosedTime();
                	
                	boolean isClosedBy247 = Test247User(updated_by);
                	if(!isClosedBy247){
                		SetCloseBySE();
                	}
                }
                
                // Set lai follow_shift_id hien tai cho incident vua duoc Reopen
                if(this.reopened_by != null && this.incident != null && this.incident.internal_status != null
            		&& this.incident.internal_status.equalsIgnoreCase("closed")
            		&& (this.internal_status == null || !this.internal_status.equalsIgnoreCase("closed")))
                {
                	UpdateShiftIDForReopenIncident();
                }
                
                String s = (new StringBuilder("UpdateToSDK: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
                MyLogger.WriteLog("INFO", query.toString());
            }
        }
        catch(Exception e)
        {
            String s = String.format("INCIDENT MODEL: UpdateToSDK %s Exception: %s", new Object[] {itsm_id, e.getMessage()});
            
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }

    private boolean Test247User(String username){
    	boolean result = false;
        try
        {
        	if(!Config.SPECIAL_247_USER.contains(username)){
		        MyMySQL sdkDB = new MyMySQL();
		    	String count_query = String.format("SELECT COUNT(1) AS count FROM %s WHERE is_247=1 AND email LIKE '%s%s' ",
		    			new Object[] {Config.MYSQL_TBL_USER, username, "%"});
		    	ResultSet rs = sdkDB.ExecuteQuery(count_query);
		        if(rs != null)
		        {
		            rs.next();
		            int count = rs.getInt("count");
		            result = count > 0;
		        }
		        String s = String.format("Test247User: %s %s %s", itsm_id, username, result);
		        System.out.println(s);
		        MyLogger.WriteLog("INFO", s);
        	} else {
        		result = true;
        		String s = String.format("Test247User: %s %s %s", itsm_id, username, "In special list");
		        System.out.println(s);
		        MyLogger.WriteLog("INFO", s);
        	}
        }
        catch(Exception e)
        {
        	String s = String.format("INCIDENT MODEL: Test247User %s Exception: %s", new Object[] {itsm_id, e.getMessage()});
            System.out.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
        
        return result;
    }
    
    private void SetCloseBySE(){
        try
        {
            MyMySQL sdkDB = new MyMySQL();
	    	String query = String.format("UPDATE %s SET closed_by_se='1',closed_by_se_username='%s' WHERE itsm_incident_id='%s'",
	    			new Object[] {Config.MYSQL_TBL_INCIDENT, updated_by, itsm_id});
	    	sdkDB.ExecuteNoneScalarQuery(query);
	    	
	    	String s = String.format("SetCloseBySE: %s", itsm_id);
            System.out.println(s);
            MyLogger.WriteLog("INFO", s);
        }
        catch(Exception e)
        {
        	String s = String.format("INCIDENT MODEL: SetCloseBySE %s Exception: %s",
        			new Object[] {itsm_id, e.getMessage()});
            System.out.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
    }
    
    private void UpdateShiftIDForReopenIncident(){
    	try
        {
            MyMySQL sdkDB = new MyMySQL();
	    	String query = String.format("UPDATE %s SET follow_shift_id=%s,closed_by_se='0',notify_closed_by_se='1',is_show='1' WHERE itsm_incident_id='%s'",
	    			new Object[] {Config.MYSQL_TBL_INCIDENT, Utils.GetShiftID(), itsm_id});
	    	sdkDB.ExecuteNoneScalarQuery(query);
	    	
	    	String s = String.format("UpdateShiftIDForReopenIncident: %s %s", itsm_id, Utils.GetShiftID());
            System.out.println(s);
            MyLogger.WriteLog("INFO", s);
        }
        catch(Exception e)
        {
            String s = String.format("INCIDENT MODEL: UpdateShiftIDForReopenIncident %s Exception: %s",
            		new Object[] {itsm_id, e.getMessage()});
            System.out.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
    }
    
    public void Update_SE_Report()
    {
        StringBuilder query = new StringBuilder();
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                query.append("UPDATE ");
                query.append(Config.MYSQL_TBL_INCIDENT);
                query.append(" SET ");
                query.append("se_reported='1' ");
                query.append("WHERE itsm_incident_id=?");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, itsm_id);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("Update_SE_Report: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = String.format("INCIDENT MODEL: Update_SE_Report %s Exception: %s",
            		new Object[] {itsm_id, e.getMessage()});
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }

    public void Close_SE_Notification()
    {
        StringBuilder query = new StringBuilder();
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                query.append("UPDATE ");
                query.append(Config.MYSQL_TBL_NOTIFICATION);
                query.append(" SET ");
                query.append("status='CLOSED' ");
                query.append("WHERE ref_id=? AND notification_type='notify_type_se' and status='OPEN'");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, itsm_id);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("Close_SE_Notification: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
        	String s = String.format("INCIDENT MODEL: Close_SE_Notification %s Exception: %s",
            		new Object[] {itsm_id, e.getMessage()});
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }
    
    public void UpdateClosedTime(){
    	StringBuilder query = new StringBuilder();
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                query.append("UPDATE ");
                query.append(Config.MYSQL_TBL_INCIDENT);
                query.append(" SET ");
                query.append("closed_time=NOW(),closed_shift_date=f_get_current_shift_date(),closed_shift_id=f_get_current_shift() ");
                query.append("WHERE itsm_incident_id=?");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, itsm_id);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("UpdateClosedTime: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = String.format("INCIDENT MODEL: UpdateClosedTime %s Exception: %s",
            		new Object[] {itsm_id, e.getMessage()});
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }
    
    public void UpdateRejectedReason(){
    	StringBuilder msQuery = new StringBuilder();
    	StringBuilder myQuery = new StringBuilder();
    	try
        {
    		/*
	    	MyMSSQL itsmDB = new MyMSSQL();
	    	
	    	msQuery.append(String.format("SELECT TOP 1 [DESCRIPTION] FROM %s WHERE [TYPE]='%s'AND [NUMBER]='%s' ORDER BY [DATESTAMP] DESC", Config.MSSQL_TBL_ACTIVITY_HISTORY, Config.ACTIVITY_TYPE_UPDATE, this.itsm_id));
	    	ResultSet rs = itsmDB.ExecuteQuery(msQuery.toString());
			if(rs != null && rs.next()){
				try{
					String strRejectedReason = rs.getString("DESCRIPTION");
					MyMySQL sdkDB = new MyMySQL();
					myQuery = myQuery.append(String.format("UPDATE %s SET rejected_reason=? WHERE itsm_incident_id='%s'", Config.MYSQL_TBL_INCIDENT, this.itsm_id));
					
					Connection conn = sdkDB.GetConnection();
	                PreparedStatement stament = conn.prepareStatement(myQuery.toString());
	                stament.setString(1, strRejectedReason);
	                stament.execute();
	                stament.close();
	                
	                String s = String.format("UpdateRejectedReason: %s %s", itsm_id, strRejectedReason);
	                System.out.println(s);
	                MyLogger.WriteLog("INFO", s);
				}
				catch(Exception e)
		        {
		            String s = String.format("INCIDENT MODEL: GetRejectedReason %s Exception: %s",
		            		new Object[] {itsm_id, e.getMessage()});
		            String q = myQuery.toString();
		            System.out.println(s);
		            System.out.println(q);
		            MyLogger.WriteLog("EXCEPTION", s);
		            MyLogger.WriteLog("EXCEPTION", q);
		        }
			}
			*/
    		MyMySQL sdkDB = new MyMySQL();
			myQuery = myQuery.append(String.format("UPDATE %s SET rejected_reason=update_action WHERE itsm_incident_id='%s'", Config.MYSQL_TBL_INCIDENT, this.itsm_id));
			Connection conn = sdkDB.GetConnection();
            PreparedStatement stament = conn.prepareStatement(myQuery.toString());
			stament.execute();
            stament.close();
        }
        catch(Exception e)
        {
            String s = String.format("INCIDENT MODEL: GetRejectedReason %s Exception: %s",
            		new Object[] {itsm_id, e.getMessage()});
            String q = msQuery.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }
}
