#include "UpdateAlertSttProcess.h"

#include "../Controller/AlertSyncController.h"
#include "../Controller/G8AlertController.h"
#include "../Controller/CSAlertController.h"
#include "../Controller/AlertController.h"
#include "../Model/G8AlertModel.h"
#include "../Model/CSAlertModel.h"
#include "../Model/ZbxAlertModel.h"
#include "../Controller/IncidentFollowController.h"
#include "../Config/SyncProcessData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"
#include "../ExternalService/CurlService.h"

CUpdateAlertSttProcess::CUpdateAlertSttProcess(void)
{

}

CUpdateAlertSttProcess::CUpdateAlertSttProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CUpdateAlertSttProcess::~CUpdateAlertSttProcess(void)
{
	Destroy();
}

void CUpdateAlertSttProcess::Init()
{
	//=============================//
	m_pIncidentFollowController = new CIncidentFollowController();
	m_pCSAlertModel 			= new CCSAlertModel();
	m_pG8AlertModel 			= new CG8AlertModel();
	m_pCSAlertController 		= new CCSAlertController();
	m_pG8AlertController 		= new CG8AlertController();
	ControllerConnect();
}

void CUpdateAlertSttProcess::Destroy()
{
	delete m_pCSAlertController;
	delete m_pG8AlertController;
	delete m_pCSAlertModel;
	delete m_pG8AlertModel;
	delete m_pIncidentFollowController;
	delete m_pConfigFile;
}

ConnectInfo CUpdateAlertSttProcess::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	if(m_pConfigFile->GetPort().compare("") != 0)
		CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}

bool CUpdateAlertSttProcess::ControllerConnect()
{
	//====================================MONGO MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pCSAlertController->Connect(CInfo))
		return false;
	if(!m_pG8AlertController->Connect(CInfo))
		return false;
	//================================MYSQL  MA Connection====================================
	CInfo = GetConnectInfo(MYSQL_MA);
	if(!m_pIncidentFollowController->Connect(CInfo))
		return false;
	cout<< "Connected\n";
	return true;
}

MA_RESULT CUpdateAlertSttProcess::ProcessRejectCSAlert()
{
	string strItsmId, strItsmStatus, strTicketId, strSourceId, strMsg, strLog;
	while(true)
	{
		if(!m_pCSAlertController->FindDB(m_pCSAlertModel->GetQueryCsReject())){
			m_pCSAlertController->DestroyData();
			sleep(10);
			continue;
		}
		while(m_pCSAlertController->NextRecord())
		{
			strSourceId 	= CUtilities::GetMongoObjId(m_pCSAlertController->GetStringResultVal(RECORD_ID));
			strTicketId 	= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(TICKET_ID));
			strItsmStatus 	= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(ITSM_STATUS));
			strItsmId 		= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(ITSM_ID));
			strMsg 			= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(MSG));
			if(strItsmId.compare("null") == 0 || strItsmId.compare("EOO") == 0)
			{
				m_pCSAlertModel->SetObjId(strSourceId);
				m_pCSAlertModel->SetTicketId(strTicketId);
				m_pCSAlertModel->SetITSMStatus(strItsmStatus);
				m_pCSAlertModel->SetItsmId("null");
				m_pCSAlertModel->SetRejectMsg(strMsg);
				m_pCSAlertModel->PrepareRecord(); 
				if(m_pCSAlertController->UpdateStatusINC(m_pCSAlertModel->GetRecordBson(), "")) // CS API Update Status from Inc
				// if(1) // CS API Update Status from Inc
				{
					m_pCSAlertController->UpdateCSReject(m_pCSAlertModel->GetObjectIdQuery()); // Update cs_alerts itsm id
					strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessRejectCSAlert_API", "CS Alert Source Id:" + strSourceId);
					CUtilities::WriteErrorLog(strLog);
				}
				else
				{
					strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessRejectCSAlert_API", "FAIL:CS Alert Source Id:" + strSourceId);
					CUtilities::WriteErrorLog(strLog);
				}
			}
			m_pCSAlertModel->DestroyData();
		}
		m_pCSAlertController->DestroyData();
		sleep(60);
	}
}

MA_RESULT CUpdateAlertSttProcess::ProcessUpdate()
{
	string strJsonSourceInfo, strSourceFrom, strCurrStatus, strTicketId, strItsmId, strLog; //
	CJsonModel objJsonModel;
	int nSize, iItsmSttNoti;
	AlertInfo sAlertInf;
	while(true)
	{
		if(!m_pIncidentFollowController->FindIncidentInDay()){
			m_pIncidentFollowController->ResetModel();
			sleep(10);
			continue;
		}
		m_pIncidentFollowController->GetFieldName();
		while(m_pIncidentFollowController->NextRow())
		{
			strSourceFrom = sAlertInf.strSourceId = strCurrStatus = strTicketId = strItsmId = sAlertInf.strOutageEnd = sAlertInf.strStatus = ""; 
			sAlertInf.iStatus = iItsmSttNoti = 0;
			//===============Json SourceInfo===============
			strJsonSourceInfo = m_pIncidentFollowController->ModelGetString(LINKED_ALERTS);
			if(strJsonSourceInfo.empty() || strJsonSourceInfo.compare("[]") == 0)
				continue;
			objJsonModel.AppendArray(strJsonSourceInfo);
			objJsonModel.GoToIndex(JSON_ROOT);
			nSize = objJsonModel.GetSize();
			if(nSize == 0)
				continue;
			//=============================================
			sAlertInf.strOutageEnd = CUtilities::ToLowerString(m_pIncidentFollowController->ModelGetString(OUTAGE_END));
			sAlertInf.strStatus = CUtilities::ToLowerString(m_pIncidentFollowController->ModelGetString(STATUS));
			sAlertInf.strMSG = m_pIncidentFollowController->ModelGetString("rejected_reason");
			// =========Get Inc Status ===========
			if(sAlertInf.strStatus.compare("open") == 0)
				sAlertInf.iStatus = RAW_ALERT_STATUS_INIT;
			else if(sAlertInf.strStatus.compare("closed") == 0)
				sAlertInf.iStatus = RAW_ALERT_STATUS_ITSM_CLOSED;
			else if(sAlertInf.strStatus.compare("reopen") == 0)
				sAlertInf.iStatus = RAW_ALERT_STATUS_ITSM_REOPEN;
			else if(sAlertInf.strStatus.compare("rejected") == 0)
				sAlertInf.iStatus = RAW_ALERT_STATUS_ITSM_REJECTED;
			else if(sAlertInf.strStatus.compare("resolved") == 0)
				sAlertInf.iStatus = RAW_ALERT_STATUS_ITSM_RESOLVED;
			//==========================Update Alert status from ITSM=======================
			for(int i=0; i<nSize; i++)
			{
				sAlertInf.strSourceId = CUtilities::RemoveBraces(objJsonModel.toString(i,"src_id"));
				strSourceFrom = CUtilities::ToUpperString(CUtilities::RemoveBraces(objJsonModel.toString(i,"src_from")));
				if(strSourceFrom.compare(CS_SOURCE_FROM_VAL) == 0)
				{
					UpdateCSAlertStatus(sAlertInf);
				}
				if(strSourceFrom.compare(G8_SOURCE_FROM_VAL) == 0)
				{ 
					UpdateG8AlertStatus(sAlertInf);
				}
			}
			objJsonModel.DestroyData();
		}
		//===========Destroy============
		m_pIncidentFollowController->ResetModel();
		sleep(60);
	}
}

bool CUpdateAlertSttProcess::UpdateCSAlertStatus(AlertInfo sAlertInf)
{
	string strCurrStatus, strTicketId, strItsmId, strLog;
	int iItsmSttNoti;
	
	m_pCSAlertController->DestroyData();
	m_pCSAlertModel->DestroyData();
	
	m_pCSAlertModel->SetObjId(sAlertInf.strSourceId);
	m_pCSAlertModel->SetStatus(sAlertInf.iStatus);
	if(sAlertInf.strStatus.compare("rejected") == 0)
		m_pCSAlertModel->SetRejectMsg(sAlertInf.strMSG);
	if(m_pCSAlertController->FindDB(m_pCSAlertModel->GetObjectIdQuery())){
	// if(m_pCSAlertController->FindDB()){
		if(m_pCSAlertController->NextRecord())
		{
			strCurrStatus 	= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(ITSM_STATUS));
			strTicketId 	= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(TICKET_ID));
			strItsmId 		= CUtilities::RemoveBraces(m_pCSAlertController->GetStringResultVal(ITSM_ID));
			iItsmSttNoti 	= m_pCSAlertController->GetIntResultVal(ITSM_STATUS_NOTI);
			m_pCSAlertModel->SetTicketId(strTicketId);
			m_pCSAlertModel->SetItsmId(strItsmId);
			//============================Outage-End is empty==========================
			if(sAlertInf.strOutageEnd.empty())
			{
				if(strCurrStatus.compare(sAlertInf.strStatus) != 0){
					m_pCSAlertModel->SetITSMStatus(sAlertInf.strStatus);
					m_pCSAlertModel->SetITSMSttNoti(1);
					m_pCSAlertModel->PrepareRecord();
					if(m_pCSAlertController->UpdateStatusINC(m_pCSAlertModel->GetRecordBson(), sAlertInf.strOutageEnd)) // CS API Update Status from Inc
					// if(true) // hieutt test
					{
						m_pCSAlertController->UpdateAlertStatus(m_pCSAlertModel->GetObjectIdQuery(), m_pCSAlertModel->GetRecordBson()); // Update cs_alerts status
						strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessCSUpdate_Call_API", sAlertInf.strSourceId + ": " + 
																			 strCurrStatus + " -> " + sAlertInf.strStatus);
						CUtilities::WriteErrorLog(strLog);
					}
					else
					{
						strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessCSUpdate_Call_API","FAIL:" + sAlertInf.strSourceId + ": " + 
																			 strCurrStatus + " -> " + sAlertInf.strStatus);
						CUtilities::WriteErrorLog(strLog);
					}
				}
			}
			//============================Outage-End isn't empty==========================
			else if(iItsmSttNoti == 1)
			{
				m_pCSAlertModel->SetITSMStatus("closed");
				m_pCSAlertModel->SetITSMSttNoti(0);
				m_pCSAlertModel->PrepareRecord();
				if(m_pCSAlertController->UpdateStatusINC(m_pCSAlertModel->GetRecordBson(), sAlertInf.strOutageEnd)) // CS API Update Status from Inc
				// if(true) // hieutt test
				{
					m_pCSAlertController->UpdateAlertStatus(m_pCSAlertModel->GetObjectIdQuery(), m_pCSAlertModel->GetRecordBson()); // Update cs_alerts status
					strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessCSUpdate_Call_API_Outage_end", sAlertInf.strSourceId + ": " + 
																		 strCurrStatus + " -> " + sAlertInf.strStatus);
					CUtilities::WriteErrorLog(strLog);
				}
				else
				{
					strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessCSUpdate_Call_API_Outage_end","FAIL:" + sAlertInf.strSourceId + ": " + 
																		 strCurrStatus + " -> " + sAlertInf.strStatus);
					CUtilities::WriteErrorLog(strLog);
				}
			}
		}
	}
}

bool CUpdateAlertSttProcess::UpdateG8AlertStatus(AlertInfo sAlertInf)
{
	string strCurrStatus, strTicketId, strItsmId, strLog, strLink;
	int iItsmSttNoti;
	m_pG8AlertController->DestroyData();
	m_pG8AlertModel->DestroyData();
	m_pG8AlertModel->SetObjId(sAlertInf.strSourceId);
	m_pG8AlertModel->SetStatus(sAlertInf.iStatus);
	if(m_pG8AlertController->FindDB(m_pG8AlertModel->GetObjectIdQuery())){
		if(m_pG8AlertController->NextRecord())
		{
			strLink 		= m_pConfigFile->ReadStringValue(UPDATE_STT_ALERT_GROUP, API_G8_UPDATE_STATUS);
			strCurrStatus 	= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(ITSM_STATUS));
			strTicketId 	= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(TICKET_ID));
			strItsmId 		= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(ITSM_ID));
			iItsmSttNoti 	= m_pG8AlertController->GetIntResultVal(ITSM_STATUS_NOTI);
			// if(strCurrStatus.compare(sAlertInf.strStatus) != 0){
			m_pG8AlertModel->SetTicketId(strTicketId);
			m_pG8AlertModel->SetItsmId(strItsmId);
			// ============================Call G8 API==========================
			if(sAlertInf.strOutageEnd.empty())
			{
				if(strCurrStatus.compare(sAlertInf.strStatus) != 0)
				{
					m_pG8AlertModel->SetITSMStatus(sAlertInf.strStatus);
					m_pG8AlertModel->SetITSMSttNoti(1);
					m_pG8AlertModel->PrepareRecord();
					if(m_pG8AlertController->UpdateStatusINC(strLink ,m_pG8AlertModel->GetRecordBson(), sAlertInf.strOutageEnd)) // G8 API Update Status from Inc
					// if(true) // hieutt test
					{	
						m_pG8AlertController->UpdateAlertStatus(m_pG8AlertModel->GetObjectIdQuery(), m_pG8AlertModel->GetRecordBson()); // Update G8_alerts status
						strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessG8Update_Call_API", sAlertInf.strSourceId + ": " + 
																			 strCurrStatus + " -> " + sAlertInf.strStatus);
						CUtilities::WriteErrorLog(strLog);
					}
					else
					{
						strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessG8Update_Call_API","FAIL:" + sAlertInf.strSourceId + ": " + 
																			 strCurrStatus + " -> " + sAlertInf.strStatus);
						CUtilities::WriteErrorLog(strLog);
					}
				}
			}
			else if(iItsmSttNoti == 1)
			{
				m_pG8AlertModel->SetITSMStatus("closed");
				m_pG8AlertModel->SetITSMSttNoti(0);
				m_pG8AlertModel->PrepareRecord();
				if(m_pG8AlertController->UpdateStatusINC(strLink ,m_pG8AlertModel->GetRecordBson(), sAlertInf.strOutageEnd)) // G8 API Update Status from Inc
				// if(true) // hieutt test
				{
					m_pG8AlertController->UpdateAlertStatus(m_pG8AlertModel->GetObjectIdQuery(), m_pG8AlertModel->GetRecordBson()); // Update G8_alerts status
					strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessG8Update_Call_API_Outage_end", sAlertInf.strSourceId + ": " + 
																		 strCurrStatus + " -> " + sAlertInf.strStatus);
					CUtilities::WriteErrorLog(strLog);
				}
				else
				{
					strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "ProcessG8Update_Call_API_Outage_end","FAIL:" + sAlertInf.strSourceId + ": " + 
																		 strCurrStatus + " -> " + sAlertInf.strStatus);
					CUtilities::WriteErrorLog(strLog);
				}
			}
		}
	}
}
