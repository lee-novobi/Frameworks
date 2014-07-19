package Model;

import Helper.*;

import java.sql.*;

public class AffectedCIModel
{

    public String logical_name;
    public String relationship_name;
    public int record_number;
    public String related_ci;
    public String created_date;

    public AffectedCIModel()
    {
    }

    public void WriteToSDK()
    {
        InsertToSDK();
    }

    public boolean IsExistedInSDK()
    {
    	return false;
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
                /*--*/query.append(Config.MYSQL_TBL_AFFECTED_CI);
                /*--*/query.append(" (");
                /*01*/query.append("logical_name,");
                /*02*/query.append("relationship_name,");
                /*03*/query.append("record_number,");
                /*04*/query.append("related_ci,");
                /*05*/query.append("created_date)");
                /*--*/query.append(" VALUES(?,?,?,?,?)");

                Connection conn = sdkDB.GetConnection();
                PreparedStatement stament = conn.prepareStatement(query.toString());
                stament.setString(1, logical_name);
                stament.setString(2, relationship_name);
                stament.setInt(3, record_number);
                stament.setString(4, related_ci);
                stament.setString(5, created_date);
                stament.execute();
                stament.close();
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("AffectedCIModel - InsertToSDK Exception: ")).append(e.getMessage()).toString();
            String q = query.toString();
            System.out.println(s);
            System.out.println(q);
            MyLogger.WriteLog("EXCEPTION", s);
            MyLogger.WriteLog("INFO", q);
        }
    }

    public void UpdateToSDK()
    {
    }
    
    public void Truncate(){
    	try
        {
	    	MyMySQL sdkDB = new MyMySQL();
	    	
	    	Connection conn = sdkDB.GetConnection();
			String query = "TRUNCATE " + Config.MYSQL_TBL_AFFECTED_CI;
			conn.createStatement().execute(query);
        } catch(Exception e) {
            String s = (new StringBuilder("AffectedCIModel - Truncate Exception: ")).append(e.getMessage()).toString();
            System.out.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
            
        }
    }
}
