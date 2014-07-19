package Helper;

import Model.IncidentModel;
import java.io.PrintStream;
import java.sql.ResultSet;

// Referenced classes of package Helper:
//            MyMSSQL, MyLogger

public class Syncher
{

    public Syncher()
    {
    }

    public static void SynSEReport(String from)
    {
        String query = null;
        if(from != null)
        {
            query = String.format("%s'%s'", new Object[] {
                "select distinct a.inc_id INC_ID from all_incident2010 a inner join sysattachmem1" +
" at on(a.inc_id=at.topic) where a.impact<='2' AND lower(a.resolve_by_sdk)='se' AND" +
" (filename like '%incident report%' or filename like '%incident_report%') and a." +
"sysmodtime >= "
, from
            });
        } else
        {
            query = "select distinct a.inc_id INC_ID from all_incident2010 a inner join sysattachmem1" +
" at on(a.inc_id=at.topic) where a.impact<='2' AND lower(a.resolve_by_sdk)='se' AND" +
" (filename like '%incident report%' or filename like '%incident_report%')"
;
        }
        try
        {
            MyMSSQL itsmDB = new MyMSSQL();
            if(itsmDB.TestConnection())
            {
                ResultSet rs_se_report = itsmDB.ExecuteQuery(query);
                if(rs_se_report != null)
                {
                    IncidentModel model = new IncidentModel();
                    for(; rs_se_report.next(); model.Close_SE_Notification())
                    {
                        model.itsm_id = rs_se_report.getString("INC_ID");
                        model.Update_SE_Report();
                    }

                    String s = "Helper SynchFromITSM-SynchSE_Report: Synchronized";
                    System.out.println(s);
                    MyLogger.WriteLog("INFO", s);
                    MyLogger.WriteLog("INFO", query);
                }
            } else
            {
                String s = "Helper SynchFromITSM-SynchSE_Report: Test ITSM connection fail";
                System.out.println(s);
                MyLogger.WriteLog("INFO", s);
            }
            itsmDB.CloseConnection();
        }
        catch(Exception e)
        {
            String s = (new StringBuilder("Helper SynchFromITSM-SynchSE_Report Exception: ")).append(e.getMessage()).toString();
            System.err.println(s);
            MyLogger.WriteLog("EXCEPTION", s);
        }
    }
}
