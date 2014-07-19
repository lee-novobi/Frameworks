#include "UpdateCSImpactProcess.h"

#include "../Controller/IncidentController.h"
#include "../Controller/ImpactLevelController.h"
#include "../Controller/CSAlertController.h"
#include "../Model/ImpactLevelModel.h"
#include "../Model/CSAlertModel.h"
#include "../Config/SyncProcessData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CUpdateCSImpactProcess::CUpdateCSImpactProcess(void)
{

}

CUpdateCSImpactProcess::CUpdateCSImpactProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CUpdateCSImpactProcess::~CUpdateCSImpactProcess(void)
{
	Destroy();
}

void CUpdateCSImpactProcess::Init()
{
	//==========Construction===========//
	m_pIncidentController 		= new CIncidentController();
	m_pImpactLevelController 	= new CImpactLevelController();
	m_pCSAlertController 		= new CCSAlertController();
	m_pImpactLevelModel 		= new CImpactLevelModel();
	m_pCSAlertModel 			= new CCSAlertModel();
	//=============Connection============//
	ControllerConnect();
}

void CUpdateCSImpactProcess::Destroy()
{
	delete m_pIncidentController;
	delete m_pImpactLevelController;
	delete m_pCSAlertController;
	delete m_pImpactLevelModel;
	delete m_pCSAlertModel;
	delete m_pConfigFile;
}

ConnectInfo CUpdateCSImpactProcess::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost 	= m_pConfigFile->GetHost();
	CInfo.strUser 	= m_pConfigFile->GetUser();
	CInfo.strPass 	= m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	if(m_pConfigFile->GetPort().compare("") != 0)
		CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}

bool CUpdateCSImpactProcess::ControllerConnect()
{
	//=============MONGODB==============
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pCSAlertController->Connect(CInfo))
		return false;
	if(!m_pImpactLevelController->Connect(CInfo))
		return false;
	//================MySQL====================
	CInfo = GetConnectInfo(MYSQL_MA);
	if(!m_pIncidentController->Connect(CInfo))
		return false;
	cout<< "Connected\n";
	return true;
}


bool CUpdateCSImpactProcess::Reconnect()
{
	if(!m_pIncidentController->Reconnect())
		return false;
	return true;
}

MA_RESULT CUpdateCSImpactProcess::ProcessUpdate()
{
	IncUpdateHistoryInfo stIncUpdHis;
	string strSourceId, strItsmId, strTicketId;
	string strLog;
	int iNumOfCase, iImpactLevel, iPrevImpactLevel, iITSMCaseNoti, iIsLinked, iAutoUpdateImpactLevel;
	try{
		while(true){
			while(!Reconnect())
				sleep(5);
			m_pCSAlertController->DestroyData();
			m_pCSAlertController->FindDB();
			while(m_pCSAlertController->NextRecord())
			{
				iITSMCaseNoti = iNumOfCase = iImpactLevel = iPrevImpactLevel = iIsLinked = iAutoUpdateImpactLevel = -1;
				strSourceId = strItsmId = strTicketId = "";
				//===========Destroy==============//
				m_pImpactLevelController->DestroyData();
				m_pImpactLevelModel->DestroyData();
				m_pCSAlertModel->DestroyData();
				m_pIncidentController->ResetModel();
				//===========Get New Impact Level==============//
				iNumOfCase = m_pCSAlertController->GetIntResultVal(NUM_OF_CASE);
				m_pImpactLevelModel->SetSourceForm(CS_SOURCE_FROM_VAL);
				m_pImpactLevelModel->SetNumOfCase(iNumOfCase);
				m_pImpactLevelModel->PrepareRecord();
				if(m_pImpactLevelController->FindDB(m_pImpactLevelModel->GetImpactLevelByCaseNumQuery())){
					m_pImpactLevelController->NextRecord();
					iImpactLevel = m_pImpactLevelController->GetIntResultVal(IMPACT_LEVEL);
					iPrevImpactLevel = m_pCSAlertController->GetIntResultVal(IMPACT_LEVEL);
					strItsmId = CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(ITSM_ID));
				}
				//=====================================Update New Impact Level==============================================//
				iITSMCaseNoti = m_pCSAlertController->GetIntResultVal(ITSM_CASE);
				if(iITSMCaseNoti != 1)
				{
					try{
						// ======================Update Impact Level to CS_Alerts====================//
						iIsLinked = m_pCSAlertController->GetIntResultVal("is_linked");
						iAutoUpdateImpactLevel = m_pCSAlertController->GetIntResultVal("auto_update_impact_level");
						strTicketId = CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(TICKET_ID));
						strSourceId = CUtilities::GetMongoObjId(m_pCSAlertController->GetStringResultVal(RECORD_ID));
						m_pCSAlertModel->SetObjId(strSourceId);
						m_pCSAlertModel->SetImpactLevel(iImpactLevel);
						m_pCSAlertModel->SetImpactUpdateTime(CUtilities::GetCurrTime());
						m_pCSAlertModel->SetImpactUpdateUnixTime(atol(CUtilities::GetCurrTimeStamp().c_str()));
						m_pCSAlertModel->SetSdkItsmNoti(0);
						m_pCSAlertModel->SetItsmCase(1);
						m_pCSAlertModel->PrepareRecord();
						m_pCSAlertController->UpdateCSImpact(m_pCSAlertModel->GetObjectIdQuery(), m_pCSAlertModel->GetRecordBson());
						strLog = CUtilities::FormatLog(LOG_MSG, "UpdateCSImpact", "ProcessUpdate","UpdateImpact:cs_alerts|SourceId:" + strSourceId + "|ITSM_Id:" + strItsmId);
						CUtilities::WriteErrorLog(strLog);
						// ======================Update Case and Impact Level to ITSM====================//
						if(strItsmId.compare("null") != 0 && strItsmId.compare("EOO") != 0 && !strItsmId.empty()){
							if(iIsLinked != 1)
							{
								if(iAutoUpdateImpactLevel == 1){
									stIncUpdHis.iSdkUpdateToItsmStatus 	= 0;
									stIncUpdHis.iImpactLvl 				= iImpactLevel;
									stIncUpdHis.iCustomerCase 			= iNumOfCase;
									stIncUpdHis.strItsmIncId 			= strItsmId;
									if(m_pIncidentController->InsertCSImpact(stIncUpdHis)){
										strLog = CUtilities::FormatLog(LOG_MSG, "UpdateCSImpact", "ProcessUpdate","UpdateImpact:itsm|SourceId:" + strSourceId + "|" + "ITSM_Id:" + strItsmId);
										CUtilities::WriteErrorLog(strLog);
									}
									else
									{
										strLog = CUtilities::FormatLog(LOG_MSG, "UpdateCSImpact", "ProcessUpdate","FAIL|UpdateImpact:itsm|SourceId:" +  strSourceId + "|" + "ITSM_Id:" + strItsmId);
										CUtilities::WriteErrorLog(strLog);
									}
								}
								else
								{
									if(iImpactLevel != iPrevImpactLevel)
									{
										m_pIncidentController->NotiImpactLvlUp("cs_alert", strTicketId, iImpactLevel);
										strLog = CUtilities::FormatLog(LOG_MSG, "UpdateCSImpact", "ProcessUpdate","UpdateImpact:Noti|SourceId:" + strSourceId + "|ITSM_Id:" + strItsmId);
										CUtilities::WriteErrorLog(strLog);
									}
								}
							}
						}
					}
					catch(exception& ex)
					{
						string strLog;
						stringstream strErrorMess;
						strErrorMess<<ex.what()<<"][";
						strErrorMess<< __FILE__<< "|" << __LINE__;
						strLog = CUtilities::FormatLog(BUG_MSG, "UpdateCSImpact", "ProcessUpdate",strErrorMess.str());
						CUtilities::WriteErrorLog(strLog);
					}
					
				}
			}
			// sleep(10);// hieutt test
			sleep(300); 
		}
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "UpdateCSImpact", "ProcessUpdate",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}
