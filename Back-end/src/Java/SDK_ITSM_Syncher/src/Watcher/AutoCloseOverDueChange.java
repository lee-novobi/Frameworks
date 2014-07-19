package Watcher;

import Helper.MyLogger;
import Helper.MyMySQL;

public class AutoCloseOverDueChange {
	public void Run(){
		try{		
			String query = "UPDATE change_follow SET internal_status='closed' WHERE planned_end<NOW()";
			new MyMySQL().ExecuteNoneScalarQuery(query);
		} catch (Exception e){
			String s = "AutoCloseOverDueChange Exception: " + e.getMessage();
			System.err.println(s);
			MyLogger.WriteLog("EXCEPTION", s);
		}
	}
}
