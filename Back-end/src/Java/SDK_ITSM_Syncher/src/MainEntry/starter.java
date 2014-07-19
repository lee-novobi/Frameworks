package MainEntry;

import Helper.Config;
import Helper.MyLogger;
import Helper.Syncher;
import Watcher.*;

import java.text.*;
import java.util.*;

public class starter
{

    public starter()
    {
    }

    public static void main(String args[])
    {
        try
        {
            Config.ReadConfigFile();
            // SyncherIncident inc_syncher = new SyncherIncident();
            // inc_syncher.SynchFromITSM();
            //SyncherDepartment department_syncher = new SyncherDepartment();
            //department_syncher.GetDepartmentListFromITSM();
            //SyncherProduct product_syncher1 = new SyncherProduct();
            //product_syncher1.GetProductListFromITSM();
            // return;
            //SyncherTask syncher = new SyncherTask();
            //SyncherIncidentRelated syncher = new SyncherIncidentRelated();
            //syncher.SynchFromITSM();
            // --------------------------------------------------------------------------------- //
//            Timer incTimer = new Timer();
//            incTimer.schedule(new TimerTask() {
//
//                public void run()
//                {
//                    SyncherIncident inc_syncher = new SyncherIncident();
//                    inc_syncher.SynchFromITSM();
//                }
//
//            }, 100, Config.WATCHER_LOOP_PERIOR_INCIDENT);
            
            if(Config.SYNCH_PRODUCT_AT_START)
            {
                SyncherProduct product_syncher = new SyncherProduct();
                product_syncher.GetProductListFromITSM();
            }
            // --------------------------------------------------------------------------------- //
            if(Config.SYNCH_AFFECTED_CI_AT_START)
            {
                SyncherAffectedCI syncher = new SyncherAffectedCI();
                syncher.SynchFromITSM();
            }
            // --------------------------------------------------------------------------------- //
            if(Config.SYNCH_DEPARTMENT_AT_START)
            {
                Timer timer = new Timer();
                timer.schedule(new TimerTask() {

                    public void run()
                    {
                        SyncherDepartment department_syncher = new SyncherDepartment();
                        department_syncher.GetDepartmentListFromITSM();
                    }

                }, 30000L);
            }
            // --------------------------------------------------------------------------------- //
            if(Config.SYNCH_ASSIGNMENT_GROUP_AT_START)
            {
                Timer timer = new Timer();
                timer.schedule(new TimerTask() {

                    public void run()
                    {
                        SyncherAssigmnentGroup assignment_syncher = new SyncherAssigmnentGroup();
                        assignment_syncher.GetAssignmentGroupListFromITSM();
                    }

                }, 60000L);
            }
            // --------------------------------------------------------------------------------- //
            if(Config.SYNCH_ASSIGNEE_AT_START)
            {
                Timer timer = new Timer();
                timer.schedule(new TimerTask() {

                    public void run()
                    {
                        SyncherAssignee assignee_syncher = new SyncherAssignee();
                        assignee_syncher.GetAssigneeListFromITSM();
                    }

                }, 0x15f90L);
            }
            // --------------------------------------------------------------------------------- //
            if(Config.SYNCH_SE_REPORT_AT_START)
            {
                Timer timer = new Timer();
                timer.schedule(new TimerTask() {

                    public void run()
                    {
                        Syncher.SynSEReport(null);
                    }

                }, 0x1d4c0L);
            }
            // --------------------------------------------------------------------------------- //
            Timer incTimer = new Timer();
            incTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherIncident inc_syncher = new SyncherIncident();
                    inc_syncher.SynchFromITSM();
                }

            }, 150000, Config.WATCHER_LOOP_PERIOR_INCIDENT);
            // --------------------------------------------------------------------------------- //
            Timer chnTimer = new Timer();
            chnTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherChange chn_syncher = new SyncherChange();
                    chn_syncher.SynchFromITSM();
                    (new AutoCloseOverDueChange()).Run();
                }

            }, 180000, Config.WATCHER_LOOP_PERIOR_CHANGE);
            // --------------------------------------------------------------------------------- //
            Timer taskTimer = new Timer();
            taskTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherTask task_syncher = new SyncherTask();
                    task_syncher.SynchFromITSM();
                }

            }, 210000, Config.WATCHER_LOOP_PERIOR_TASK);
            // --------------------------------------------------------------------------------- //
            Timer incidentRelatedTimer = new Timer();
            incidentRelatedTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherIncidentRelated incident_related_syncher = new SyncherIncidentRelated();
            		incident_related_syncher.SynchFromITSM();
                }

            }, 210000, Config.WATCHER_LOOP_PERIOR_TASK);
            // --------------------------------------------------------------------------------- //
            Timer assigneeTimer = new Timer();
            assigneeTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherAssignee assignee_syncher = new SyncherAssignee();
                    assignee_syncher.GetAssigneeListFromITSM();
                }

            }, GetDistanceToSpecTime(Config.SYNCH_ASSIGNEE_AT), Config.WATCHER_LOOP_PERIOR_ASSIGNEE);
            // --------------------------------------------------------------------------------- //
            Timer assignmentTimer = new Timer();
            assignmentTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherAssigmnentGroup assignment_syncher = new SyncherAssigmnentGroup();
                    assignment_syncher.GetAssignmentGroupListFromITSM();
                }

            }, GetDistanceToSpecTime(Config.SYNCH_ASSIGNMENT_GROUP_AT), Config.WATCHER_LOOP_PERIOR_ASSIGNMENT);
            // --------------------------------------------------------------------------------- //
            Timer productTimer = new Timer();
            productTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherProduct product_syncher = new SyncherProduct();
                    product_syncher.GetProductListFromITSM();
                }

            }, GetDistanceToSpecTime(Config.SYNCH_PRODUCT_AT), Config.WATCHER_LOOP_PERIOR_PRODUCT);
            // --------------------------------------------------------------------------------- //
            Timer departmentTimer = new Timer();
            departmentTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherDepartment department_syncher = new SyncherDepartment();
                    department_syncher.GetDepartmentListFromITSM();
                }

            }, GetDistanceToSpecTime(Config.SYNCH_DEPARTMENT_AT), Config.WATCHER_LOOP_PERIOR_DEPARTMENT);
            // --------------------------------------------------------------------------------- //
            Timer afciTimer = new Timer();
            afciTimer.schedule(new TimerTask() {

                public void run()
                {
                    SyncherDepartment department_syncher = new SyncherDepartment();
                    department_syncher.GetDepartmentListFromITSM();
                }

            }, GetDistanceToSpecTime(Config.SYNCH_DEPARTMENT_AT), Config.WATCHER_LOOP_PERIOR_DEPARTMENT);
            // --------------------------------------------------------------------------------- //
        }
        catch(Exception e)
        {
            System.out.println(e.getMessage());
        }
    }

    public static long GetDistanceToSpecTime(String specTime)
    {
    	try{
	        DateFormat dtfm2;
	        String date;
	        DateFormat dtfm1 = new SimpleDateFormat("yyyy-MM-dd");
	        dtfm2 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
	        date = String.format("%s %s", new Object[] {
	            dtfm1.format(new Date()), specTime
	        });
	        long runAt;
	        runAt = dtfm2.parse(date).getTime();
	        if(runAt < System.currentTimeMillis())
	        {
	            Calendar c = Calendar.getInstance();
	            c.setTime(dtfm2.parse(date));
	            c.add(5, 1);
	            runAt = c.getTimeInMillis();
	        }
	        return runAt - System.currentTimeMillis();
    	} catch (Exception e){
    		System.err.println("Starter GetDistanceToSpecTime Exception: " + e.getMessage());
            MyLogger.WriteLog("ERROR", "Starter GetDistanceToSpecTime Exception: " + e.getMessage());
            e.printStackTrace();
            return 0L;
    	}
    }
}
