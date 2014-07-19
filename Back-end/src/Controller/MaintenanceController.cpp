#include "MaintenanceController.h"
#include "../Common/DBCommon.h"
#include "../Config/ConfigFileParse.h"
#include "../Model/MaintenanceModel.h"
#include "../Model/TopMaintenanceModel.h"
#include "ChangesController.h"

#define MAINTENANCE_ON 1
#define MAINTENANCE_OFF 0
#define CHANGED	1
#define DISPLAY 1
#define SOURCE_ZBX "Zabbix"

CMaintenanceController::CMaintenanceController(void)
{
	m_pConfigFile = NULL;
}
CMaintenanceController::CMaintenanceController(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
}

CMaintenanceController::~CMaintenanceController(void)
{
	if (NULL != m_pConfigFile)
		delete m_pConfigFile;
}

/******************************************************************************
 *                                                                            *
 * Function: DayInMonth                                                     *
 *                                                                            *
 * Purpose: returns number of days in a month                                 *
 *                                                                            *
 * Parameters: year - year, month - month (0-11)                              *
 *                                                                            *
 * Return value: 28-31 depending on number of days in the month               *
 *                                                                            *
 * Author: Alexander Vladishev                                                *
 *                                                                            *
 * Comments:                                                                  *
 *                                                                            *
 ******************************************************************************/
int	CMaintenanceController::DayInMonth(int year, int mon)
{
#define is_leap_year(year) (((year % 4) == 0 && (year % 100) != 0) || (year % 400) == 0)
	unsigned char month[12] = { 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 };
	unsigned char month_leap[12] = { 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 };

	if (is_leap_year(year))
		return month_leap[mon];
	else
		return month[mon];
}
/******************************************************************************
 *                                                                            *
 * Function: GetTopMaintenanceInfo                                            *
 *                                                                            *
 * Purpose: returns list top maintenance model				                  *
 *                                                                            *
 ******************************************************************************/
vector<long long> CMaintenanceController::GetTopMaintenanceInfo(ConnectInfo CInfo, time_t now)
{
	vector<long long> vtMaintenanceId;
	
	CMaintenanceModel objMaintenanceModel;			// Maintenance Model Object
	CTopMaintenanceModel objTopMaintenanceModel;	// Top Maintenance Model Object
	CChangesController	objChangesController;		// Changes Controller Object
	
	string strMaintenanceCollection;				// Maintenance Name Collection
	
	//Changes object controller connect mongodb
	objChangesController.Connect(CInfo);	
	
	//Get Maintenance Name Active Collection
	strMaintenanceCollection = objChangesController.GetActiveCollection(MAINTENANCES);
	
	int iTopPeriod;									// Top Period : 2 hours
	
	string strMaintenanceName;						// Name value of Maintenance Collection

	

	//********************************* maintenance algorithm ****************************
	int						day, week, wday, sec;
	struct tm				*tm;
	long long				db_maintenanceid;
	time_t					db_active_since, active_since, db_start_date, maintenance_from, maintenance_to;
	zbx_timeperiod_type_t	db_timeperiod_type;
	int						db_every, db_month, db_dayofweek, db_day, db_start_time,
							db_period, db_maintenance_type;

	tm = CUtilities::GetLocalTime(&now);
	sec = tm->tm_hour * SEC_PER_HOUR + tm->tm_min * SEC_PER_MIN + tm->tm_sec;

	Query qMaintenance = objMaintenanceModel.GetListMaintenanceInfo();
	
	if (FindDB(strMaintenanceCollection, qMaintenance))
	{	
		while(NextRecord())
		{
			db_maintenanceid	= (long long)GetLongResultVal("maintenanceid");
			strMaintenanceName  = GetFieldString("name");
			db_maintenance_type	= GetIntResultVal("maintenance_type");
			db_active_since		= (time_t)GetIntResultVal("active_since");
			db_timeperiod_type	= (zbx_timeperiod_type_t)GetIntResultVal("timeperiod_type");
			db_every			= GetIntResultVal("every");
			db_month			= GetIntResultVal("month");	
			db_dayofweek		= GetIntResultVal("dayofweek");
			db_day				= GetIntResultVal("day");
			db_start_time		= GetIntResultVal("start_time");
			db_period			= GetIntResultVal("period");
			db_start_date		= GetIntResultVal("start_date");

			switch (db_timeperiod_type) {
			case TIMEPERIOD_TYPE_ONETIME:
				break;
			case TIMEPERIOD_TYPE_DAILY:
				db_start_date = now - sec + db_start_time;
				if (sec < db_start_time)
					db_start_date -= SEC_PER_DAY;

				if (db_start_date < db_active_since)
					continue;

				tm = CUtilities::GetLocalTime(&db_active_since);
				active_since = db_active_since - (tm->tm_hour * SEC_PER_HOUR + tm->tm_min * SEC_PER_MIN + tm->tm_sec);

				day = (db_start_date - active_since) / SEC_PER_DAY + 1;
				db_start_date -= SEC_PER_DAY * (day % db_every);
				break;
			case TIMEPERIOD_TYPE_WEEKLY:
				db_start_date = now - sec + db_start_time;
				if (sec < db_start_time)
					db_start_date -= SEC_PER_DAY;

				if (db_start_date < db_active_since)
					continue;

				tm = CUtilities::GetLocalTime(&db_active_since);
				wday = (0 == tm->tm_wday ? 7 : tm->tm_wday) - 1;
				active_since = db_active_since - (wday * SEC_PER_DAY + tm->tm_hour * SEC_PER_HOUR + tm->tm_min * SEC_PER_MIN + tm->tm_sec);

				for (; db_start_date >= db_active_since; db_start_date -= SEC_PER_DAY)
				{
					/* check for every x week(s) */
					week = (db_start_date - active_since) / SEC_PER_WEEK + 1;
					if (0 != (week % db_every))
						continue;

					/* check for day of the week */
					tm = CUtilities::GetLocalTime(&db_start_date);
					wday = (0 == tm->tm_wday ? 7 : tm->tm_wday) - 1;
					if (0 == (db_dayofweek & (1 << wday)))
						continue;

					break;
				}
				break;
			case TIMEPERIOD_TYPE_MONTHLY:
				db_start_date = now - sec + db_start_time;
				if (sec < db_start_time)
					db_start_date -= SEC_PER_DAY;

				for (; db_start_date >= db_active_since; db_start_date -= SEC_PER_DAY)
				{
					/* check for month */
					tm = CUtilities::GetLocalTime(&db_start_date);
					if (0 == (db_month & (1 << tm->tm_mon)))
						continue;

					if (0 != db_day)
					{
						/* check for day of the month */
						if (db_day != tm->tm_mday)
							continue;
					}
					else
					{
						/* check for day of the week */
						wday = (0 == tm->tm_wday ? 7 : tm->tm_wday) - 1;
						if (0 == (db_dayofweek & (1 << wday)))
							continue;

						/* check for number of day (first, second, third, fourth or last) */
						day = (tm->tm_mday - 1) / 7 + 1;
						if (5 == db_every && 4 == day)
						{
							if (tm->tm_mday + 7 <= DayInMonth(tm->tm_year, tm->tm_mon))
								continue;
						}
						else if (db_every != day)
							continue;
					}

					break;
				}
				break;
			default:
				continue;
			}
		
			if (db_start_date < db_active_since)
				continue;
			
			if (db_start_date > now || now >= db_start_date + db_period)
				continue;

			maintenance_from = db_start_date;
			
			vtMaintenanceId.push_back(db_maintenanceid);
			//***************************** end maintenance algorithm *******************************************
		}
	}

	return vtMaintenanceId;
}
/******************************************************************************
 *                                                                            *
 * Function: GetListHostMaintenance                                           *
 *                                                                            *
 * Purpose: returns list host maintenance					                  *
 *                                                                            *
 ******************************************************************************/
vector<long long> CMaintenanceController::GetListHostMaintenance(ConnectInfo CInfo, long long lMaintenanceId)
{
	vector<long long> vtHostId;

	CMaintenanceModel objMaintenanceModel;				// Maintenance Model Object
	CMaintenanceController objMaintenanceController;	// Maintenance Controller wrapper object
	CChangesController	objChangesController;			// Changes Controller Object 
	
	string strMaintenancesHostsCollection;				// Maintenances Hosts Name Collection
	
	try
	{
		//Connect to mongodb
		objChangesController.Connect(CInfo);
		objMaintenanceController.Connect(CInfo);

		//Get Name Active of Maintenances Hosts Collection
		strMaintenancesHostsCollection = objChangesController.GetActiveCollection(MAINTENANCES_HOSTS);
		
		//Get query list host maintenance
		Query qMaintenancesHosts = objMaintenanceModel.GetListHostMaintenanceInfo(lMaintenanceId);
		
		if(objMaintenanceController.FindDB(strMaintenancesHostsCollection, qMaintenancesHosts))
		{
			while(objMaintenanceController.NextRecord())
			{
				vtHostId.push_back(objMaintenanceController.GetLongResultVal("hostid"));
			}
		}
	}
	catch(exception& ex)
	{	
		string strFormatLog;
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CMaintenanceController", "GetListHostMaintenance", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	return vtHostId;
}
/******************************************************************************
 *                                                                            *
 * Function: SaveTopMaintenanceInfo							                  *
 *                                                                            *
 * Purpose: Save top maintenace info into collection		                  *
 *                                                                            *
 ******************************************************************************/
void CMaintenanceController::SaveTopMaintenanceInfo(ConnectInfo CInfo)
{
	vector<long long> vtMaintenanceId;
	
	int iTopPeriod;
	int iPerUnit;
	int iSum;
	int iClock;

	time_t now;

	now = time(NULL);
	struct tm	*tm;
	tm = CUtilities::GetLocalTime(&now);
	iClock = now - tm->tm_sec;

	//Get Top Period from config
	try
	{
		iTopPeriod = atoi(m_pConfigFile->GetData(MAINTENANCE_CONFIG, TIMER_TOP_PERIOD).c_str());
	}
	catch(exception& ex)
	{
		iTopPeriod = 0;
	}
	try
	{
		iPerUnit = atoi(m_pConfigFile->GetData(MAINTENANCE_CONFIG, TIMER_PER_UNIT).c_str());
	}
	catch(exception& ex)
	{
		iPerUnit = 1;
	}
	
	iSum = (int)iTopPeriod / iPerUnit;
	
	try
	{
		for(int i = 0; i <= iSum; i++)
		{
			BSONObj pClockCond = BSON("clock" << iClock);
			CTopMaintenanceModel *pTopMaintenanceModel = new CTopMaintenanceModel();
			vtMaintenanceId = GetTopMaintenanceInfo(CInfo, iClock);
	
			pTopMaintenanceModel->SetClock(iClock);
			pTopMaintenanceModel->SetListMaintenance(vtMaintenanceId);
			pTopMaintenanceModel->PrepareRecord();
			InsertDB(TOP_MAINTENANCES, pClockCond, pTopMaintenanceModel->GetRecordBson());

			iClock += iPerUnit;

			vtMaintenanceId.clear();
			delete pTopMaintenanceModel;
		}
	}
	catch(exception& ex)
	{	
		string strFormatLog;
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CMaintenanceController", "SaveTopMaintenanceInfo", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
}

/******************************************************************************
 *                                                                            *
 * Function: ComputeMaintenanceInfo							                  *
 *                                                                            *
 * Purpose: Compute Maintenance Info for host				                  *
 *                                                                            *
 ******************************************************************************/
void CMaintenanceController::ComputeMaintenanceInfo(ConnectInfo CInfo)
{
	CTopMaintenanceModel objTopMaintenanceModel;
	CMaintenanceModel objMaintenanceModel;

	CMaintenanceController objMaintenanceController;
	vector<long long> vtMaintenanceId;
	vector<long long> vtHostId;

	int iClock;
	long long lMaintenanceId;
	long long lHostId;

	string strDataInfo="";
	string strFormatLog;

	time_t now;

	now = time(NULL);
	struct tm	*tm;
	tm = CUtilities::GetLocalTime(&now);
	iClock = now - tm->tm_sec;

	try
	{
		objMaintenanceController.Connect(CInfo);
		Query query = objTopMaintenanceModel.QueryMaintenanceByClock(iClock);

		if (objMaintenanceController.FindDB(TOP_MAINTENANCES, query))
		{
			BSONArrayBuilder baHostObj;
			BSONArrayBuilder baNotHostObj;

			BSONObj boDataInfo;

			BSONObjBuilder bbConditionInfo;
			BSONObjBuilder bbNotHostInfo;

			while(objMaintenanceController.NextRecord())
			{
				vtMaintenanceId = objMaintenanceController.GetArrayLongResultVal("maintenanceid");

				for (int i=0; i < vtMaintenanceId.size(); i++)
				{
					lMaintenanceId = vtMaintenanceId[i];
					
					vtHostId = GetListHostMaintenance(CInfo, lMaintenanceId);
					
					for (int j=0; j < vtHostId.size(); j++)
					{
						lHostId = vtHostId[j];
						baHostObj.append(lHostId);
						baNotHostObj.append(lHostId);
					}
				}
				
				if (baHostObj.arrSize() > 0)
				{
					//Update Maintenance
					bbConditionInfo << "zabbix_server_id" << BSON("$in" << baHostObj.arr());
					boDataInfo = BSON("zbx_maintenance" << MAINTENANCE_ON);		
					if (objMaintenanceController.UpdateDB(MONITORING_ASSISTANT_ALERTS, bbConditionInfo.asTempObj(), boDataInfo))
					{
						//Track Info Update
						strDataInfo = "";
						strDataInfo += "update " + (string)MONITORING_ASSISTANT_ALERTS + " set:" + boDataInfo.jsonString();
						strDataInfo += " where:" + bbConditionInfo.asTempObj().jsonString();
						strFormatLog = CUtilities::FormatLog(INFO_MSG, "CMaintenanceController", "ComputeMaintenanceInfo", strDataInfo);				
						CUtilities::WriteDataLog(strFormatLog);
					}

					//Update Not Maintenance
					bbNotHostInfo << "is_show" << DISPLAY << "source_from" << SOURCE_ZBX;
					bbNotHostInfo << "zabbix_server_id" << BSON("$nin" << baNotHostObj.arr());
					boDataInfo = BSON("zbx_maintenance" << MAINTENANCE_OFF);
					if (objMaintenanceController.UpdateDB(MONITORING_ASSISTANT_ALERTS, bbNotHostInfo.asTempObj(), boDataInfo))
					{
						//Track Info Update
						strDataInfo = "";
						strDataInfo += "update " + (string)MONITORING_ASSISTANT_ALERTS + " set:" + boDataInfo.jsonString();
						strDataInfo += " where:" + bbNotHostInfo.asTempObj().jsonString();
						strFormatLog = CUtilities::FormatLog(INFO_MSG, "CMaintenanceController", "ComputeMaintenanceInfo", strDataInfo);				
						CUtilities::WriteDataLog(strFormatLog);
					}
				}
			}
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CMaintenanceController", "ComputeMaintenanceInfo", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
}

/******************************************************************************
 *                                                                            *
 * Function: RotateTopMaintenanceInfo						                  *
 *                                                                            *
 * Purpose: Rotate Top Maintenance Old Info 				                  *
 *                                                                            *
 ******************************************************************************/
void CMaintenanceController::RotateTopMaintenanceInfo(ConnectInfo CInfo)
{
	CTopMaintenanceModel objTopMaintenanceModel;
	CMaintenanceController objMaintenanceController;
	

	int iClock;
	int iTimeLimit;
	int iRotatePeriod;

	long long lMaintenanceId;
	long long lHostId;

	time_t now;

	now = time(NULL);
	struct tm	*tm;
	tm = CUtilities::GetLocalTime(&now);
	iClock = now - tm->tm_sec;
	iRotatePeriod = atoi(m_pConfigFile->GetData(MAINTENANCE_CONFIG, TIMER_ROTATE_TOP_MAINTENANCE).c_str());
	iTimeLimit = iClock - iRotatePeriod;

	try
	{
		objMaintenanceController.Connect(CInfo);
		Query query = objTopMaintenanceModel.QueryMaintenanceWithOutScope(iTimeLimit);
		objMaintenanceController.RemoveDB(TOP_MAINTENANCES, query);
	}
	catch(exception& ex)
	{	
		string strFormatLog;
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CMaintenanceController", "ComputeMaintenanceInfo", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
}