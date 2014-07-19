package Model;

import Helper.*;
import java.io.PrintStream;
import java.sql.*;

public class TaskModel
{

    public String itsm_id;
    public String category;
    public String status;
    public String requested_by;
    public String assigned_to;
    public String assigned_dept;
    public String planned_start;
    public String planned_end;
    public String current_phase;
    public String date_entered;
    public String parent_change;
    public String description;
    public String brief_desc;
    public String down_start;
    public String down_end;
    public String actual_start;
    public String actual_end;
    public String last_modify;
    public String created_date;
    public String close_time;

    public TaskModel()
    {
    }

    public void WriteToSDK()
    {
        if(itsm_id != null && itsm_id.length() > 0){
            if(IsExistedInSDK()){
                UpdateToSDK();
            } else  {
                InsertToSDK();
            }
        }
    }

    public boolean IsExistedInSDK()
    {
        boolean result = false;
        try{
            MyMySQL sdkDB = new MyMySQL();
            if(sdkDB.TestConnection())
            {
                String count_query = String.format("SELECT COUNT(1) AS count FROM %s WHERE itsm_task_id='%s'", new Object[] {
                    Config.MYSQL_TBL_TASK_ITSM, itsm_id
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
            String s = (new StringBuilder("TaskModel - IsExistedInSDK Exception: ")).append(e.getMessage()).toString();
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
                /*--*/query.append(Config.MYSQL_TBL_TASK_ITSM);
                /*--*/query.append(" (");
                /*01*/query.append("itsm_task_id,");
                /*02*/query.append("category,");
                /*03*/query.append("status,");
                /*04*/query.append("requested_by,");
                /*05*/query.append("assigned_to,");
                /*06*/query.append("assigned_dept,");
                /*07*/query.append("planned_start,");
                /*08*/query.append("planned_end,");
                /*09*/query.append("current_phase,");
                /*10*/query.append("date_entered,");
                /*11*/query.append("parent_change,");
                /*12*/query.append("description,");
                /*13*/query.append("brief_desc,");
                /*14*/query.append("down_start,");
                /*15*/query.append("down_end,");
                /*16*/query.append("actual_start,");
                /*17*/query.append("actual_end,");
                /*18*/query.append("last_modify,");
                /*19*/query.append("created_date,");
                /*20*/query.append("close_time)");
                /*--*/query.append(" VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, itsm_id);
                stament.setString(2, category);
                stament.setString(3, status);
                stament.setString(4, requested_by);
                stament.setString(5, assigned_to);
                stament.setString(6, assigned_dept);
                stament.setString(7, planned_start);
                stament.setString(8, planned_end);
                stament.setString(9, current_phase);
                stament.setString(10, date_entered);
                stament.setString(11, parent_change);
                stament.setString(12, description);
                stament.setString(13, brief_desc);
                stament.setString(14, down_start);
                stament.setString(15, down_end);
                stament.setString(16, actual_start);
                stament.setString(17, actual_end);
                stament.setString(18, last_modify);
                stament.setString(19, created_date);
                stament.setString(20, close_time);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("TaskModel - InsertToSDK: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("TaskModel - InsertToSDK Exception: ")).append(e.getMessage()).toString();
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
                /*--*/query.append("UPDATE ");
                /*--*/query.append(Config.MYSQL_TBL_TASK_ITSM);
                /*--*/query.append(" SET ");
                /*01*/query.append("category=?,");
                /*02*/query.append("status=?,");
                /*03*/query.append("requested_by=?,");
                /*04*/query.append("assigned_to=?,");
                /*05*/query.append("assigned_dept=?,");
                /*06*/query.append("planned_start=?,");
                /*07*/query.append("planned_end=?,");
                /*08*/query.append("current_phase=?,");
                /*09*/query.append("date_entered=?,");
                /*10*/query.append("parent_change=?,");
                /*11*/query.append("description=?,");
                /*12*/query.append("brief_desc=?,");
                /*13*/query.append("down_start=?,");
                /*14*/query.append("down_end=?,");
                /*15*/query.append("actual_start=?,");
                /*16*/query.append("actual_end=?,");
                /*17*/query.append("last_modify=?,");
                /*18*/query.append("close_time=? ");
                /*19*/query.append("WHERE itsm_task_id=?");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, category);
                stament.setString(2, status);
                stament.setString(3, requested_by);
                stament.setString(4, assigned_to);
                stament.setString(5, assigned_dept);
                stament.setString(6, planned_start);
                stament.setString(7, planned_end);
                stament.setString(8, current_phase);
                stament.setString(9, date_entered);
                stament.setString(10, parent_change);
                stament.setString(11, description);
                stament.setString(12, brief_desc);
                stament.setString(13, down_start);
                stament.setString(14, down_end);
                stament.setString(15, actual_start);
                stament.setString(16, actual_end);
                stament.setString(17, last_modify);
                stament.setString(18, close_time);
                stament.setString(19, itsm_id);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("TaskModel - UpdateToSDK: ")).append(itsm_id).toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("TaskModel - UpdateToSDK Exception: ")).append(e.getMessage()).toString();
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }
}
