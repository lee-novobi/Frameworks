package Watcher;

import Helper.*;
import Model.ChangeModel;
import java.io.PrintStream;
import java.sql.ResultSet;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class SyncherChange
{

    public SyncherChange()
    {
    }

    public void SynchFromITSM()
    {
        try
        {
            synchronized(this)
            {
                String lastSynch = Config.SynchFileGetProperty("CHANGE_LAST_SYNCH");
                String synchDistance = Config.SynchFileGetProperty("CHANGE_SYNCH_DISTANCE");
                String specificList = Config.SynchFileGetProperty("CHANGE_SYNCH_SPECIFIC");
                boolean run = Boolean.parseBoolean(Config.SynchFileGetProperty("IS_SYNCH_CHANGE"));
                if(run)
                {
                    SynchFromITSM(lastSynch, synchDistance, specificList);
                    DateFormat dtfm = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                    Config.SynchFileSetProperty("CHANGE_LAST_SYNCH", dtfm.format(new Date()));
                } else
                {
                    String s = "Change Syncher: OFF";
                    System.err.println(s);
                    MyLogger.WriteLog("INFO", s);
                }
            }
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("GetChangeListFromITSM Exception: ")).append(e.getMessage()).toString();
            System.err.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
    }

    private void SynchFromITSM(String lastSynch, String synchDistance, String specificList)
    {
        try
        {
            MyMSSQL itsmDB = new MyMSSQL();
            DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            Calendar c1 = Calendar.getInstance();
            Date lastSynchTime = new Date();
            int distance = Config.WATCHER_SYNCH_BACK_DISTANCE;
            try
            {
                distance = Integer.parseInt(synchDistance);
            }
            catch(Exception exception) { }
            try
            {
                lastSynchTime = formatter.parse(lastSynch);
            }
            catch(Exception exception1) { }
            c1.setTime(lastSynchTime);
            c1.add(12, -distance);
            String synchFrom = formatter.format(c1.getTime());
            if(itsmDB.TestConnection())
            {
                String count_query = String.format("SELECT COUNT(1) AS waiting_count FROM %s WHERE ([SYSMODTIME]>='%s')", new Object[] {
                    Config.MSSQL_TBL_CHANGE, synchFrom
                });
                ResultSet rs = itsmDB.ExecuteQuery(count_query);
                if(rs != null)
                {
                    rs.next();
                    int waiting_count = rs.getInt("waiting_count");
                    if(waiting_count > 0)
                    {
                        StringBuilder query = new StringBuilder();
                        query.append("SELECT [CM3RM1].[PLAN],[CM3RM1].[BACKOUT_METHOD],[CM3RM1].[INFORM_PRODUCTS],[CM3RM1].[ASSIGN_DEPT],[CM3RM1].[COORDINATOR],");
                        query.append("CASE WHEN (cv.[PLANNED_END]<GETDATE()) THEN 'closed' ELSE NULL END AS [INTERNAL_STATUS],cv.[AFFECTED_ITEM],");
                        query.append("cv.[NUMBER],cv.[CATEGORY],cv.[STATUS],cv.[APPROVAL_STATUS],cv.[PLANNED_START],cv.[PLANNED_END],");
                        query.append("cv.[DURATION],cv.[ORIG_DATE_ENTERED],cv.[ORIG_OPERATOR],cv.[BRIEF_DESCRIPTION],cv.[DESCRIPTION],");
                        query.append("cv.[DOWN_START],cv.[DOWN_END],cv.[REQUESTEDDATE],cv.[ACTUAL_DOWNTIME_START],cv.[ACTUAL_DOWNTIME_END] ");
                        query.append(String.format("FROM %s cv INNER JOIN %s.[dbo].[CM3RM1] on(cv.number=[CM3RM1].number) WHERE ([SYSMODTIME]>='%s')", Config.MSSQL_TBL_CHANGE, Config.MSSQL_DB_NAME, synchFrom));
                        rs = itsmDB.ExecuteQuery(query.toString());
                        if(rs != null)
                        {
                            ChangeModel model = new ChangeModel();
                            for(; rs.next(); model.WriteToSDK())
                            {
                                model.itsm_change_id = rs.getString("NUMBER");
                                model.title = rs.getString("BRIEF_DESCRIPTION");
                                model.planned_start = rs.getString("PLANNED_START");
                                model.planned_end = rs.getString("PLANNED_END");
                                model.down_start = rs.getString("DOWN_START");
                                model.down_end = rs.getString("DOWN_END");
                                model.description = rs.getString("DESCRIPTION");
                                model.created_by = rs.getString("ORIG_OPERATOR");
                                model.status = rs.getString("STATUS");
                                model.internal_status = rs.getString("INTERNAL_STATUS");
                                model.plan = rs.getString("PLAN");
                                model.backout_method = rs.getString("BACKOUT_METHOD");
                                model.informed_group = rs.getString("INFORM_PRODUCTS");
                                model.assignment_group = rs.getString("ASSIGN_DEPT");
                                model.change_coordinator = rs.getString("COORDINATOR");
                                model.service = rs.getString("AFFECTED_ITEM");
                            }

                        }
                        String s = "Change: SynchFromITSM: Synchronized";
                        System.out.println(s);
                        MyLogger.WriteLog("INFO", s);
                        MyLogger.WriteLog("INFO", query.toString());
                    } else
                    {
                        String s = "Change: SynchFromITSM: Waiting Count 0";
                        System.out.println(s);
                        MyLogger.WriteLog("INFO", s);
                        MyLogger.WriteLog("INFO", count_query);
                    }
                }
            } else
            {
                String s = "Change: SynchFromITSM: Test ITSM connection fail";
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
            itsmDB.CloseConnection();
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("Change: SynchFromITSM Exception: ")).append(e.getMessage()).toString();
            System.err.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
    }
}
