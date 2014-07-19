package Model;

import Helper.*;
import java.io.PrintStream;
import java.sql.*;

public class ChangeModel
{

    public String itsm_change_id;
    public String follow_shift_id;
    public String title;
    public String created_by;
    public String status;
    public String created_date;
    public String planned_start;
    public String planned_end;
    public String internal_status;
    public String down_start;
    public String down_end;
    public String description;
    public String plan;
    public String backout_method;
    public String assignment_group;
    public String change_coordinator;
    public String service;
    public String informed_group;

    public ChangeModel()
    {
    }

    public void WriteToSDK()
    {
        if(itsm_change_id != null && itsm_change_id.length() > 0)
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
                String count_query = String.format("SELECT COUNT(1) AS count FROM %s WHERE itsm_change_id='%s'", new Object[] {
                    Config.MYSQL_TBL_CHANGE, itsm_change_id
                });
                ResultSet rs = sdkDB.ExecuteQuery(count_query);
                if(rs != null)
                {
                    rs.next();
                    int count = rs.getInt("count");
                    result = count > 0;
                }
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("CHANGE MODEL: IsExistedInSDK Exception: ")).append(e.getMessage()).toString();
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
                query.append("INSERT INTO ");
                query.append(Config.MYSQL_TBL_CHANGE);
                query.append(" (");
                query.append("title,");
                query.append("created_by,");
                query.append("created_date,");
                query.append("status,");
                query.append("planned_start,");
                query.append("planned_end,");
                query.append("down_start,");
                query.append("down_end,");
                query.append("description,");
                query.append("follow_shift_id,");
                query.append("internal_status,");
                query.append("plan,");
                query.append("backout_method,");
                query.append("assignment_group,");
                query.append("change_coordinator,");
                query.append("service,");
                query.append("informed_groups,");
                query.append("itsm_change_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, title);
                stament.setString(2, created_by);
                stament.setString(3, Utils.GetShiftDate());
                stament.setString(4, status);
                stament.setString(5, planned_start);
                stament.setString(6, planned_end);
                stament.setString(7, down_start);
                stament.setString(8, down_end);
                stament.setString(9, description);
                stament.setInt(10, Utils.GetShiftID());
                stament.setString(11, internal_status);
                stament.setString(12, plan);
                stament.setString(13, backout_method);
                stament.setString(14, assignment_group);
                stament.setString(15, change_coordinator);
                stament.setString(16, service);
                stament.setString(17, informed_group);
                stament.setString(18, itsm_change_id);
                stament.execute();
                String s = (new StringBuilder("InsertToSDK Change: ")).append(itsm_change_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("CHANGE MODEL: InsertToSDK Exception: ")).append(e.getMessage()).toString();
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
        try
        {
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                query.append("UPDATE ");
                query.append(Config.MYSQL_TBL_CHANGE);
                query.append(" SET ");
                query.append("title=?,");
                query.append("created_by=?,");
                query.append("created_date=?,");
                query.append("status=?,");
                query.append("planned_start=?,");
                query.append("planned_end=?,");
                query.append("down_start=?,");
                query.append("down_end=?,");
                query.append("description=?,");
                query.append("internal_status=?,");
                query.append("plan=?,");
                query.append("backout_method=?,");
                query.append("assignment_group=?,");
                query.append("change_coordinator=?,");
                query.append("service=?,");
                query.append("informed_groups=? ");
                query.append("WHERE itsm_change_id=?");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, title);
                stament.setString(2, created_by);
                stament.setString(3, created_date);
                stament.setString(4, status);
                stament.setString(5, planned_start);
                stament.setString(6, planned_end);
                stament.setString(7, down_start);
                stament.setString(8, down_end);
                stament.setString(9, description);
                stament.setString(10, internal_status);
                stament.setString(11, plan);
                stament.setString(12, backout_method);
                stament.setString(13, assignment_group);
                stament.setString(14, change_coordinator);
                stament.setString(15, service);
                stament.setString(16, informed_group);
                stament.setString(17, itsm_change_id);
                stament.execute();
                String s = (new StringBuilder("UpdateToSDK Change: ")).append(itsm_change_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("MODEL CHANGE: UpdateToSDK Exception: ")).append(e.getMessage()).toString();
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }
}
