package Helper;

import java.io.PrintStream;
import java.sql.*;

// Referenced classes of package Helper:
//            Config, MyLogger

public class MyMySQL
{

    private static Connection conn = null;
    private final String url;

    public MyMySQL()
    {
        url = String.format("jdbc:mysql://%s/%s?useUnicode=true&characterEncoding=UTF8&characterSetResults=UTF8"
        		, Config.MYSQL_DB_HOST, Config.MYSQL_DB_NAME);
    }

    public boolean TestConnection()
    {
    	try{
	        if(conn == null || conn.isClosed())
	        {
	            GetConnection();
	        }
	        if(conn == null)
	        {
	        	System.err.println("MyMySQL TestConnection Fail");
	            MyLogger.WriteLog("ERROR", "MyMySQL TestConnection Fail");
	        }
	        Statement stmt = conn.createStatement();
	        String qry = "SELECT 1";
	        stmt.executeQuery(qry);
	        return true;
    	} catch (Exception e){
    		System.err.println((new StringBuilder("MyMySQL TestConnection Exception: ")).append(e.getMessage()).toString());
    		MyLogger.WriteLog("EXCEPTION", e.getMessage());
    		return false;
    	}
    }

    public Connection GetConnection()
    {
        try
        {
            if(conn == null || conn.isClosed())
            {
                Class.forName("com.mysql.jdbc.Driver").newInstance();
                conn = DriverManager.getConnection(url, Config.MYSQL_DB_USER, Config.MYSQL_DB_PASS);
            }
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("MyMySQL GetConnection Exception: ")).append(e.getMessage()).toString());
            MyLogger.WriteLog("EXCEPTION", e.getMessage());
        }
        return conn;
    }

    public ResultSet ExecuteQuery(String query)
    {
    	try{
	    	if(query == null || query.length() <= 0)
	        {
	        	System.err.println("MyMySQL ExecuteQuery Error: Executed empty query");
	            MyLogger.WriteLog("ERROR", "MyMySQL ExecuteQuery Error: Executed empty query");
	            return null;
	        }
	        ResultSet rs;
	        Connection conn = GetConnection();
	        if(conn == null || conn.isClosed())
	        {
	        	System.err.println("MyMSSQL ExecuteQuery Error: Can not get connection");
	            MyLogger.WriteLog("ERROR", "ExecuteQuery Error: Can not get connection");
	            return null;
	        }
        
	        Statement stmt = conn.createStatement();
	        rs = stmt.executeQuery(query);
	        return rs;
        }
        catch (Exception e){
	        System.err.println((new StringBuilder("MyMySQL ExecuteQuery Exception: ")).append(e.getMessage()).toString());
	        e.printStackTrace();
	        MyLogger.WriteLog("EXCEPTION", e.getMessage());
	        return null;
        }
    }

    public boolean ExecuteNoneScalarQuery(String query)
    {
        boolean result = false;
        if(query != null && query.length() > 0)
        {
            try
            {
                Connection conn = GetConnection();
                if(conn != null && !conn.isClosed())
                {
                    Statement stmt = conn.createStatement();
                    result = stmt.execute(query);
                }
            }
            catch(Exception e)
            {
                System.err.println((new StringBuilder("MyMySQL ExecuteQuery Exception: ")).append(e.getMessage()).toString());
                e.printStackTrace();
                MyLogger.WriteLog("EXCEPTION", e.getMessage());
            }
        }
        return result;
    }

    public void CloseConnection()
    {
        try
        {
            if(conn != null && !conn.isClosed())
            {
                conn.close();
            }
        }
        catch(Exception e)
        {
            e.printStackTrace();
            MyLogger.WriteLog("EXCEPTION", e.getMessage());
        }
    }

}
