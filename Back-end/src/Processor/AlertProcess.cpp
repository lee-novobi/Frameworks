#include "AlertProcess.h"

#include "../Controller/AlertController.h"
#include "../Controller/EventController.h"
#include "../Controller/TriggerController.h"
#include "../Controller/FunctionController.h"
#include "../Controller/ItemController.h"
#include "../Controller/HostMDRController.h"
#include "../Controller/HostZabbixController.h"
#include "../Model/AlertModel.h"
#include "../Model/EventModel.h"
#include "../Model/TriggerModel.h"
#include "../Model/FunctionModel.h"
#include "../Model/ItemModel.h"
#include "../Config/AlertProcessData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

#include "mongo/client/dbclient.h"
using namespace mongo;

CAlertProcess::CAlertProcess(void)
{

}

CAlertProcess::CAlertProcess(string strCfgFile)
{
	
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CAlertProcess::~CAlertProcess(void)
{
	Destroy();
}

void CAlertProcess::Init()
{
	string strInfo;
	//======Read Config File======//
	strInfo = m_pConfigFile->ReadStringValue(ALERTGROUP,INFOPATH);
	//=============================//
	
	m_pEventController = new CEventController();
	m_pTriggerController = new CTriggerController();
	m_pAlertController = new CAlertController();
	m_pHostMDRController = new CHostMDRController();
	m_pHostZabbixController = new CHostZabbixController();
	
	m_pTriggerModel = new CTriggerModel();
	m_pAlertModel = new CAlertModel();
	m_pDataObj = new CAlertProcessData(strInfo);
	ControllerConnect();
}

void CAlertProcess::Destroy()
{
	delete m_pEventController;
	delete m_pTriggerController;
	delete m_pAlertController;
	delete m_pHostMDRController;
	delete m_pHostZabbixController;
	
	delete m_pTriggerModel;
	delete m_pAlertModel;
	delete m_pConfigFile;
	delete m_pDataObj;
}

ConnectInfo CAlertProcess::GetConnectInfo(string DBType)
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

bool CAlertProcess::ControllerConnect()
{
	//====================================MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pEventController->Connect(CInfo))
		return false;
	if(!m_pTriggerController->Connect(CInfo))
		return false;
	if(!m_pAlertController->Connect(CInfo))
		return false;
	//================================MYSQL Connection====================================
	CInfo = GetConnectInfo(MYSQL_MDR);
	if(!m_pHostMDRController->Connect(CInfo))
		return false;
	if(!m_pHostZabbixController->Connect(CInfo))
		return false;
	return true;
}

bool CAlertProcess::Reconnect()
{
	if(!m_pHostMDRController->Reconnect())
		return false;
	if(!m_pHostZabbixController->Reconnect())
		return false;
	return true;
}

MA_RESULT CAlertProcess::ProcessParse()
{
	//auto_ptr<DBClientCursor> EventCursor;
	BSONObj ItemRecord, HostRecord;
	string strTemp, strDescription, strKey, strHost, strDeptAlias, strProdAlias;
	long long lTriggerId, lItemId, lHostId, lClock, lServerId, lEventId;
	int iMaintenance, iPriorityCfg, iCheckPoint, iPos, iDesPos, nEventRecordNum, iZbxServerId, iPriority, iStatus, iValueChanged, iIsSync;
	
	//===========================Parse Log================================
	while(true)
	{
		iPriorityCfg = nEventRecordNum = 0;
		iPriorityCfg = m_pConfigFile->ReadIntValue(ALERTGROUP,PRIORITY);
		// iPos = m_pDataObj->GetPosition(); // position in Event data
		m_pEventController->DestroyData();
		// nEventRecordNum = m_pEventController->Count();
		// if(iPos < nEventRecordNum && iPos != -1)
		while(!Reconnect())
			sleep(5);
		if(m_pEventController->FindDB(QUERY(IS_SYNC<<0)))
		{
			// if(!m_pEventController->UpdateSynced()){
				// continue;
			// }
			// iCheckPoint = 0;
			while(true)
			{
				lTriggerId = lItemId = lHostId = lClock = lServerId = lEventId = 0;
				iMaintenance = iZbxServerId = iPriority = iStatus = iValueChanged = iIsSync = 0;
				strTemp = strDescription = strKey = strHost = strDeptAlias = strProdAlias = "";
				//==========DestroyData========
				m_pTriggerModel->DestroyData();
				m_pAlertModel->DestroyData();
				m_pTriggerController->DestroyData();
				m_pAlertController->DestroyData();
				m_pHostMDRController->ResetModel();
				m_pHostZabbixController->ResetModel();
				
				if(!m_pEventController->NextRecord())
				{
					// m_pEventController->DestroyData();
					break;
				}
				m_pEventController->UpdateSynced(CUtilities::GetMongoObjId(m_pEventController->GetStringResultVal(RECORD_ID)));
				// else
				// {
					// if(iCheckPoint < iPos - 1)
					// {
						// iCheckPoint++;
						// continue;
					// }
				// }
				// iCheckPoint++;
				try{
					lTriggerId 		= m_pEventController->GetLongResultVal(TRIGGER_ID);// get triggerid field value from a Event Record		
					iZbxServerId 	= m_pEventController->GetIntResultVal(ZBX_SERVER_ID); 
					if(lTriggerId == 0 || iZbxServerId == 0)
						continue;
					m_pTriggerModel->SetTriggerId(lTriggerId);
					m_pTriggerModel->SetZbxServerId(iZbxServerId);
					m_pAlertModel->SetTriggerId(lTriggerId);
					m_pAlertModel->SetZbxServerId(iZbxServerId);
					
					if(m_pTriggerController->FindDB(Query(m_pTriggerModel->GetUniqueTriggerBson())))
					{
						m_pTriggerController->NextRecord();
						iPriority = m_pTriggerController->GetIntResultVal(PRIORITY);
						m_pAlertModel->SetPriority(iPriority);
						if(iPriority != iPriorityCfg)
							continue;
						lHostId = m_pEventController->GetLongResultVal(HOST_ID);
						m_pAlertModel->SetHostId(lHostId);
						lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
						
						m_pAlertModel->SetServerId(lServerId);
						m_pHostMDRController->ModelAppend(ZBX_SERVERID,lServerId);
						m_pHostMDRController->SetServerId(lServerId);
						m_pHostZabbixController->ModelAppend(SERVER_ID,lServerId);
						m_pHostZabbixController->SetServerId(lServerId);
						
						lClock 		= m_pEventController->GetLongResultVal(CLOCK); 
						lItemId 	= m_pEventController->GetLongResultVal(ITEM_ID);
						lEventId 	= m_pEventController->GetLongResultVal(EVENT_ID);
						iStatus 	= m_pEventController->GetIntResultVal(STATUS); 
						
						m_pAlertModel->SetClock(lClock);
						m_pAlertModel->SetItemId(lItemId);
						m_pAlertModel->SetEventId(lEventId);
						m_pAlertModel->SetStatus(iStatus);

						iValueChanged = m_pEventController->GetIntResultVal(VALUE_CHANGED); 
						m_pAlertModel->SetValueChanged(iValueChanged);
						//=========================Host=====================================
						if(m_pHostMDRController->FindOne())
						{
							m_pHostMDRController->GetFieldName();
							if(m_pHostMDRController->NextRow()){
								strDeptAlias = m_pHostMDRController->ModelGetString(CMDB_DEPT_ALIAS);
								strProdAlias = m_pHostMDRController->ModelGetString(CMDB_PROD_ALIAS);
							}
						}
						if(m_pHostZabbixController->FindOne())
						{
							m_pHostZabbixController->GetFieldName();
							if(m_pHostZabbixController->NextRow()){
								iMaintenance 	= m_pHostZabbixController->ModelGetInt(MAINTENANCE);
								strHost 		= m_pHostZabbixController->ModelGetString(HOST_NAME);
							}
						}
						
						if(!strDeptAlias.empty())
							m_pAlertModel->SetDeptAlias(strDeptAlias);
						if(!strProdAlias.empty())
							m_pAlertModel->SetProdAlias(strProdAlias);
						if(!strHost.empty())
						{
							m_pAlertModel->SetHost(strHost);
						}
						else
						{
							string strLog;
							stringstream strErrorMess;
							strErrorMess << lEventId;
							strLog = CUtilities::FormatLog(LOG_MSG, "alertProcess", "ProcessParse","EventId:" + strErrorMess.str());
							CUtilities::WriteErrorLog(strLog);
						}
						m_pAlertModel->SetMaintenance(iMaintenance);
						
						////////////////////////////////////////////////////////////////////
						
						try{
							strDescription = m_pTriggerController->GetStringResultVal(DESCRIPTION);
						}
						catch(exception& ex)
						{	
							stringstream strErrorMess;
							strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
							CUtilities::WriteErrorLog(strErrorMess.str());
						}
						
						if(!strDescription.empty())
						{
							strDescription.erase(strDescription.begin());
							strDescription.erase(strDescription.end()-1);
							iDesPos = strDescription.find("{HOSTNAME}");
							if(iDesPos != string::npos && !strHost.empty())
								strDescription = strDescription.replace(iDesPos,iDesPos+9,strHost);
							else{
								iDesPos = strDescription.find("{HOST.NAME}");
								if(iDesPos != string::npos && !strHost.empty())
									strDescription = strDescription.replace(iDesPos,iDesPos+10,strHost);
							}
						}
						m_pAlertModel->SetDescription(strDescription);
						m_pAlertModel->SetAlertId(iPos++);
						m_pAlertModel->SetIsSync(iIsSync);
						////////////////////////////////////////////////////////////////
						
						//cout << iPos << " | " << lClock <<  " | " << iZbxServerId << " | " << lEventId << " | " << iStatus << " | " << lTriggerId 
							//<< " | " << lHostId << " | " << lItemId << " | " << strDescription << " | " << iPriority << " | " <<  strKey <<endl;
						m_pAlertModel->PrepareRecord();
						m_pAlertController->InsertDB(m_pAlertModel->GetUniqueAlertBson(), m_pAlertModel->GetRecordBson());
					}
				}
				catch(exception& ex)
				{	
					stringstream strErrorMess;
					strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
					CUtilities::WriteErrorLog(strErrorMess.str());
				}
			}
		}
		else
			sleep(iParseSleepTime);
	}
	return MA_RESULT_SUCCESS;
}