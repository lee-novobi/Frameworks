package Helper;

import java.io.PrintStream;
import java.sql.*;

// Referenced classes of package Helper:
//            MyLogger, Config

public class MyMSSQL
{

    private Connection conn;

    public MyMSSQL()
    {
        conn = null;
    }

    public boolean TestConnection()
    {
    	try{
	        if(conn == null)
	        {
	            GetConnection();
	        }
	        if(conn == null)
	        {
	            System.err.println("MyMSSQL TestConnection Fail");
	            MyLogger.WriteLog("ERROR", "MyMSSQL TestConnection Fail");
	        }
	        Statement stmt = conn.createStatement();
	        String qry = "SELECT 1";
	        ResultSet rs = stmt.executeQuery(qry);
	        return true;
    	} catch (Exception e){
	        System.err.println((new StringBuilder("MyMSSQL TestConnection Exception: ")).append(e.getMessage()).toString());
	        MyLogger.WriteLog("EXCEPTION", (new StringBuilder("MyMSSQL TestConnection Exception: ")).append(e.getMessage()).toString());
	        return false;
    	}
    }

    public Connection GetConnection()
    {
        if(conn == null)
        {
            try
            {
                Class.forName(Config.MSSQL_DRIVER);
                String conn_string = String.format(Config.MSSQL_CONNECTION_STRING, new Object[] {
                    Config.MSSQL_DB_HOST, Config.MSSQL_DB_NAME, Config.MSSQL_DB_USER, Config.MSSQL_DB_PASS
                });
                conn = DriverManager.getConnection(conn_string);
            }
            catch(Exception e)
            {
                System.err.println((new StringBuilder("MyMSSQL GetConnection Exception: ")).append(e.getMessage()).toString());
                MyLogger.WriteLog("EXCEPTION", (new StringBuilder("MyMSSQL GetConnection Exception: ")).append(e.getMessage()).toString());
            }
        }
        return conn;
    }

    public ResultSet ExecuteQuery(String query)
    {
    	try {
	    	Connection conn;
	        if(query == null || query.length() <= 0) {
	        	System.err.println("MyMSSQL ExecuteQuery Error: Executed empty query");
	            MyLogger.WriteLog("ERROR", "ExecuteQuery Error: Executed empty query");
	            return null;
	        }
	        conn = GetConnection();
	        if(conn == null) {
	        	System.err.println("MyMSSQL ExecuteQuery Error: Can not get connection");
	            MyLogger.WriteLog("ERROR", "ExecuteQuery Error: Can not get connection");
	            return null;
	        }
        
	        ResultSet rs;
	        Statement stmt = conn.createStatement();
	        rs = stmt.executeQuery(query);
	        return rs;
        } catch (Exception e) {
        	System.err.println((new StringBuilder("MyMSSQL ExecuteQuery Exception: ")).append(e.getMessage()).toString());
        	MyLogger.WriteLog("EXCEPTION", (new StringBuilder("MyMSSQL ExecuteQuery Exception: ")).append(e.getMessage()).toString());
        	return null;
        }
    }

    public void CloseConnection()
    {
        if(conn != null){
            try{
                conn.close();
            }
            catch(Exception exception) { }
        }
    }
}
