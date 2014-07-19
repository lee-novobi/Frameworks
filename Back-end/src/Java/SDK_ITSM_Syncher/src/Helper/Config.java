package Helper;

import java.io.*;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Properties;

public class Config
{
	public static final int INCIDENT_SMS_RESULT_EXCEPTION      = 99;
	public static final int INCIDENT_SMS_RESULT_OK             = 1;
	public static final int INCIDENT_SMS_RESULT_ALREADY_SENT   = 2;
	public static final int INCIDENT_SMS_RESULT_TIMEOUT        = 3;
	public static final int INCIDENT_SMS_RESULT_USER_NOT_FOUND = 4;
	public static final int INCIDENT_SMS_RESULT_NO_SUBSCRIBE   = 5;
	public static final int INCIDENT_SMS_RESULT_SKIP           = 0;

    public static Properties synch_file = new Properties();
    public static String MSSQL_DRIVER = "";
    public static String MSSQL_CONNECTION_STRING = "";
    public static String MSSQL_DB_HOST = "";
    public static String MSSQL_DB_USER = "";
    public static String MSSQL_DB_PASS = "";
    public static String MSSQL_DB_NAME = "";
    public static String MYSQL_DB_HOST = "";
    public static String MYSQL_DB_USER = "";
    public static String MYSQL_DB_PASS = "";
    public static String MYSQL_DB_NAME = "";
    public static int WATCHER_LOOP_PERIOR                 = 0;
    public static String WATCHER_RESULT_PATH              = "";
    public static boolean SYNCH_PRODUCT_AT_START          = true;
    public static boolean SYNCH_DEPARTMENT_AT_START       = true;
    public static boolean SYNCH_ASSIGNMENT_GROUP_AT_START = true;
    public static boolean SYNCH_ASSIGNEE_AT_START         = true;
    public static boolean SYNCH_SE_REPORT_AT_START        = true;
    public static boolean SYNCH_AFFECTED_CI_AT_START      = true;
    public static String SYNCH_PRODUCT_AT                 = "04:00:00";
    public static String SYNCH_DEPARTMENT_AT              = "04:05:00";
    public static String SYNCH_ASSIGNMENT_GROUP_AT        = "04:10:00";
    public static String SYNCH_ASSIGNEE_AT                = "04:15:00";
    public static String SYNCH_AFFECTED_CI_AT             = "04:25:00";
    public static int WATCHER_LOOP_PERIOR_PRODUCT         = 0x5265c00;
    public static int WATCHER_LOOP_PERIOR_DEPARTMENT      = 0x5265c00;
    public static int WATCHER_LOOP_PERIOR_ASSIGNMENT      = 0x5265c00;
    public static int WATCHER_LOOP_PERIOR_ASSIGNEE        = 0x5265c00;
    public static int WATCHER_LOOP_PERIOR_INCIDENT        = 0x1d4c0;
    public static int WATCHER_LOOP_PERIOR_CHANGE          = 0x1d4c0;
    public static int WATCHER_LOOP_PERIOR_TASK            = 0x1d4c0;
    public static final String MSSQL_TBL_INCIDENT                      = "[All_Incident2010]";
    public static final String MSSQL_TBL_CHANGE                        = "[GT_SR_2010]";
    public static final String MSSQL_TBL_PRODUCT_M1                    = "DEVICE2M1";
    public static final String MSSQL_TBL_PRODUCT_M2                    = "DEVICEM2";
    public static final String MSSQL_TBL_ASSIGNMENT                    = "[ASSIGNMENTM1]";
    public static final String MSSQL_TBL_ASSIGNEE                      = "[ASSIGNMENTA1]";
    public static final String MSSQL_TBL_TASK_1                        = "[CM3TM1]";
    public static final String MSSQL_TBL_TASK_2                        = "[CM3TM2]";
    public static final String MSSQL_TBL_INCIDENT_RELATED              = "[SCRELATIONM1]";
    public static final String MSSQL_TBL_AFFECTED_CI                   = "[CIRELATIONSA1]";
    public static final String MSSQL_TBL_ACTIVITY_HISTORY              = "[ACTIVITYM1]";
    public static final String MSSQL_FIELD_PRODUCT_NAME                = "[LOGICAL_NAME]";
    public static final String MSSQL_FIELD_PRODUCT_DEPARTMENT_NAME     = "[DEPARTMENT]";
	public static final String MSSQL_FIELD_PRODUCT_OB_DATE             = "[OB_DATE]";
	public static final String MSSQL_FIELD_PRODUCT_ASSIGNMENT_GROUP_L1 = "[ASSIGNMENT]";
	public static final String MSSQL_FIELD_PRODUCT_ITSM_CODE           = "[ID]";
    public static final String MSSQL_FIELD_ASSIGNMENT_NAME             = "[NAME]";
    public static final String MSSQL_FIELD_ASSIGNEE_NAME               = "[OPERATORS]";
    public static final String MSSQL_FIELD_ASSIGNMENT_PRODUCT          = "[PRODUCT]";
    public static final String MSSQL_FIELD_ASSIGNMENT_DEPARTMENT       = "[DEPARTMENT]";
    public static final String MSSQL_FIELD_PRODUCT_ISTATUS             = "[ISTATUS]";
    public static final String MSSQL_FIELD_PRODUCT_TYPE                = "[TYPE]";
    public static final String MSSQL_FIELD_PRODUCT_SUBTYPE             = "[SUBTYPE]";
    public static final String MSSQL_FIELD_PRODUCT_COMMENTS            = "[COMMENTS]";
    public static final String MSSQL_FIELD_PRODUCT_CODE                = "[PRODUCT_CODE]";
    public static final String MSSQL_FIELD_PRODUCT_PLATFORM_TYPE       = "[PLATFORM_TYPE1]";
    public static final String MSSQL_FIELD_PRODUCT_ISTATUS_VALUE_INUSE = "In Use";
    public static int MSSQL_SELECT_LIMIT = 5000;
    public static final String MYSQL_TBL_DEPARTMENT             = "department";
    public static final String MYSQL_TBL_PRODUCT                = "product";
    public static final String MYSQL_TBL_INCIDENT               = "incident_follow";
    public static final String MYSQL_TBL_USER                   = "user";
    public static final String MYSQL_TBL_INCIDENT_RELATED       = "incident_related";
    public static final String MYSQL_TBL_CHANGE                 = "change_follow";
    public static final String MYSQL_TBL_NOTIFICATION           = "notification";
    public static final String MYSQL_TBL_TASK_ITSM              = "task_itsm";
    public static final String MYSQL_TBL_INCIDENT_HISTORY       = "incident_history";
    public static final String MYSQL_TBL_CHANGE_HISTORY         = "change_history";
    public static final String MYSQL_TBL_TASK_HISTORY           = "task_history";
    public static final String MYSQL_TBL_AFFECTED_CI            = "affected_ci";
    public static final String MYSQL_FIELD_ID_OF_TBL_DEPARTMENT = "departmentid";
    public static final String MYSQL_FIELD_ID_OF_TBL_PRODUCT    = "productid";
    
    public static int WATCHER_SYNCH_BACK_DISTANCE         = 24;
    public static String LOG_PATH                         = "/var/log/sdk_watcher/";
    public static ArrayList<String> SPECIAL_247_USER      = new ArrayList<String>();
    public static String SMS_SERVICE_URL                  = "http://monitor.vng.com.vn/services/SendSMS.php?phonenumber=";
    public static int DEFAULT_INCIDENT_SMS_NOTIFY_TIMEOUT = 5;
    public static final String SOURCE_FROM_CS = "cs";
    public static final String ACTIVITY_TYPE_UPDATE = "update";
    
    
    public static final String prop_path = System.getProperty("prop.path");
    // public static final String prop_path = "d:\\working\\vng\\sdk_dev_svn\\Projects_240\\SDK_ITSM_Syncher\\";
    // public static final String prop_path = "d:\\working\\vng\\sdk_dev_svn\\Projects_240\\MonitorAssistant\\SourceCode\\Back-end\\src\\Java\\SDK_ITSM_Syncher\\";
    // public static final String prop_path = "D:\\Working\\VNG\\SVN\\SDK_DEV\\PROJECTS\\MonitorAssistant\\SourceCode\\Back-end\\src\\Java\\SDK_ITSM_Syncher\\";

    public Config()
    {
    }

    public static void ReadConfigFile()
        throws Exception
    {
        Properties prop = new Properties();
        String fileName = (new StringBuilder(String.valueOf(prop_path))).append("app.config").toString();
        InputStream is = new FileInputStream(fileName);
        prop.load(is);
        MSSQL_DRIVER = prop.getProperty("MSSQL_DRIVER");
        MSSQL_CONNECTION_STRING = prop.getProperty("MSSQL_CONNECTION_STRING");
        MSSQL_DB_HOST = prop.getProperty("MSSQL_DB_HOST");
        MSSQL_DB_USER = prop.getProperty("MSSQL_DB_USER");
        MSSQL_DB_PASS = prop.getProperty("MSSQL_DB_PASS");
        MSSQL_DB_NAME = prop.getProperty("MSSQL_DB_NAME");
        MYSQL_DB_HOST = prop.getProperty("MYSQL_DB_HOST");
        MYSQL_DB_USER = prop.getProperty("MYSQL_DB_USER");
        MYSQL_DB_PASS = prop.getProperty("MYSQL_DB_PASS");
        MYSQL_DB_NAME = prop.getProperty("MYSQL_DB_NAME");
        SYNCH_PRODUCT_AT = prop.getProperty("SYNCH_PRODUCT_AT");
        SYNCH_DEPARTMENT_AT = prop.getProperty("SYNCH_DEPARTMENT_AT");
        SYNCH_ASSIGNMENT_GROUP_AT = prop.getProperty("SYNCH_ASSIGNMENT_GROUP_AT");
        SYNCH_ASSIGNEE_AT = prop.getProperty("SYNCH_ASSIGNEE_AT");
        SYNCH_AFFECTED_CI_AT = prop.getProperty("SYNCH_AFFECTED_CI_AT");
        try
        {
            WATCHER_LOOP_PERIOR_PRODUCT = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_PRODUCT"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_PRODUCT Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_LOOP_PERIOR_DEPARTMENT = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_DEPARTMENT"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_DEPARTMENT Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_LOOP_PERIOR_ASSIGNMENT = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_ASSIGNMENT"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_ASSIGNMENT Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_LOOP_PERIOR_ASSIGNEE = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_ASSIGNEE"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_ASSIGNEE Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_LOOP_PERIOR_INCIDENT = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_INCIDENT"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_INCIDENT Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_LOOP_PERIOR_CHANGE = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_CHANGE"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_CHANGE Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_LOOP_PERIOR_TASK = Integer.parseInt(prop.getProperty("WATCHER_LOOP_PERIOR_TASK"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_LOOP_PERIOR_TASK Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            MSSQL_SELECT_LIMIT = Integer.parseInt(prop.getProperty("MSSQL_SELECT_LIMIT"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config MSSQL_PRODUCT_SELECT_LIMIT Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            WATCHER_SYNCH_BACK_DISTANCE = Integer.parseInt(prop.getProperty("WATCHER_SYNCH_BACK_DISTANCE"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config WATCHER_SYNCH_BACK_DISTANCE Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            SYNCH_PRODUCT_AT_START = Boolean.parseBoolean(prop.getProperty("SYNCH_PRODUCT_AT_START"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_PRODUCT_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            SYNCH_DEPARTMENT_AT_START = Boolean.parseBoolean(prop.getProperty("SYNCH_DEPARTMENT_AT_START"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_DEPARTMENT_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            SYNCH_ASSIGNMENT_GROUP_AT_START = Boolean.parseBoolean(prop.getProperty("SYNCH_ASSIGNMENT_GROUP_AT_START"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_ASSIGNMENT_GROUP_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            SYNCH_ASSIGNEE_AT_START = Boolean.parseBoolean(prop.getProperty("SYNCH_ASSIGNEE_AT_START"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_ASSIGNEE_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            SYNCH_SE_REPORT_AT_START = Boolean.parseBoolean(prop.getProperty("SYNCH_SE_REPORT_AT_START"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_SE_REPORT_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
        	SYNCH_AFFECTED_CI_AT_START = Boolean.parseBoolean(prop.getProperty("SYNCH_AFFECTED_CI_AT_START"));
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_SE_REPORT_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        try
        {
            String special247 = prop.getProperty("SPECIAL_247_USER");
            if(special247 != null){
            	Collections.addAll(SPECIAL_247_USER, special247.split(","));
            }
            
        }
        catch(Exception e)
        {
            System.err.println((new StringBuilder("Parse Config SYNCH_SE_REPORT_AT_START Exception: ")).append(e.getMessage()).toString());
        }
        LOG_PATH = prop.getProperty("LOG_PATH");
        SMS_SERVICE_URL = prop.getProperty("SMS_SERVICE_URL");
        
        System.out.println((new StringBuilder("MSSQL_DB_HOST: ")).append(MSSQL_DB_HOST).toString());
        System.out.println((new StringBuilder("MSSQL_DB_NAME: ")).append(MSSQL_DB_NAME).toString());
        System.out.println((new StringBuilder("MYSQL_DB_HOST: ")).append(MYSQL_DB_HOST).toString());
        System.out.println((new StringBuilder("MYSQL_DB_NAME: ")).append(MYSQL_DB_NAME).toString());
    }

    public static String SynchFileGetProperty(String key)
    {
        try{
	    	Properties properties = synch_file;
	        String fileName = (new StringBuilder(String.valueOf(prop_path))).append("app.synch").toString();
	        String rs;
	        InputStream is = new FileInputStream(fileName);
	        synch_file.load(is);
	        rs = synch_file.getProperty(key);
	        is.close();
	        return rs;
        } catch (Exception e){
        	e.printStackTrace();
        	return "";
        }
    }

    public static void SynchFileSetProperty(String key, String value)
    {
        synchronized(synch_file)
        {
            String fileName = (new StringBuilder(String.valueOf(prop_path))).append("app.synch").toString();
            try
            {
                InputStream is = new FileInputStream(fileName);
                synch_file.load(is);
                is.close();
                synch_file.setProperty(key, value);
                FileOutputStream os = new FileOutputStream(fileName);
                synch_file.store(os, null);
                os.close();
            }
            catch(Exception e)
            {
                e.printStackTrace();
            }
        }
    }

}
