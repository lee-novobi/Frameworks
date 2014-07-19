package Model;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

import Helper.Config;
import Helper.MyLogger;
import Helper.MyMySQL;

public class IncidentRelatedModel {
	public String source;
    public String source_filename;
    public String depend;
    public String depend_filename;
    public String type;
    public String source_active;
    public String depend_active;
    public String sysmodcount;
    public String sysmoduser;
    public String sysmodtime;
    public String desc;
    public String cartimeid;

    public IncidentRelatedModel(){}

    public void WriteToSDK()
    {
        if(source != null && source.length() > 0 && depend != null && depend.length() > 0)
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
                String count_query = String.format("SELECT COUNT(1) AS count FROM %s WHERE source='%s' AND depend='%s'", new Object[] {
                    Config.MYSQL_TBL_INCIDENT_RELATED, source, depend
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
            String s = (new StringBuilder("IncidentRelatedModel - IsExistedInSDK Exception: ")).append(e.getMessage()).toString();
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
                /*--*/query.append(Config.MYSQL_TBL_INCIDENT_RELATED);
                /*--*/query.append(" (");
                /*01*/query.append("source,");
                /*02*/query.append("source_filename,");
                /*03*/query.append("depend,");
                /*04*/query.append("depend_filename,");
                /*05*/query.append("type,");
                /*06*/query.append("source_active,");
                /*07*/query.append("depend_active,");
                /*08*/query.append("sysmodcount,");
                /*09*/query.append("sysmoduser,");
                /*10*/query.append("sysmodtime,");
                /*11*/query.append("`desc`,");
                /*12*/query.append("cartimeid,");
                /*--*/query.append("created_date)");
                /*--*/query.append(" VALUES(?,?,?,?,?,?,?,?,?,?,?,?,NOW())");

                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, source);
                stament.setString(2, source_filename);
                stament.setString(3, depend);
                stament.setString(4, depend_filename);
                stament.setString(5, type);
                stament.setString(6, source_active);
                stament.setString(7, depend_active);
                stament.setString(8, sysmodcount);
                stament.setString(9, sysmoduser);
                stament.setString(10, sysmodtime);
                stament.setString(11, desc);
                stament.setString(12, cartimeid);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("IncidentRelatedModel - InsertToSDK: ")).append("[S:" + source + "][D:" + depend + "]").toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("IncidentRelatedModel - InsertToSDK Exception: ")).append(e.getMessage()).toString();
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
                /*--*/query.append(Config.MYSQL_TBL_INCIDENT_RELATED);
                /*--*/query.append(" SET ");
                /*01*/query.append("source_filename=?,");
                /*02*/query.append("depend_filename=?,");
                /*03*/query.append("type=?,");
                /*04*/query.append("source_active=?,");
                /*05*/query.append("depend_active=?,");
                /*06*/query.append("sysmodcount=?,");
                /*07*/query.append("sysmoduser=?,");
                /*08*/query.append("sysmodtime=?,");
                /*09*/query.append("`desc`=?,");
                /*10*/query.append("cartimeid=? ");
                /*11-12*/query.append("WHERE source=? AND depend=?");
                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, source_filename);
                stament.setString(2, depend_filename);
                stament.setString(3, type);
                stament.setString(4, source_active);
                stament.setString(5, depend_active);
                stament.setString(6, sysmodcount);
                stament.setString(7, sysmoduser);
                stament.setString(8, sysmodtime);
                stament.setString(9, desc);
                stament.setString(10, cartimeid);
                stament.setString(11, source);
                stament.setString(12, depend);
                stament.execute();
                stament.close();
                String s = (new StringBuilder("IncidentRelatedModel - UpdateToSDK: ")).append("[S:" + source + "][D:" + depend + "]").toString();
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("IncidentRelatedModel - UpdateToSDK Exception: ")).append(e.getMessage()).toString();
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("EXCEPTION", q);
        }
    }
}
