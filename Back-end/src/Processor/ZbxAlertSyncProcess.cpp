#include "ZbxAlertSyncProcess.h"
#include "../Controller/AlertController.h"
#include "../Controller/AlertACKController.h"
#include "../Controller/AlertSyncController.h"
#include "../Model/MongodbModel.h"
#include "../Model/AlertSyncModel.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"
#include "mongo/client/dbclient.h"
using namespace mongo;

CZbxAlertSyncProcess::CZbxAlertSyncProcess(void)
{
	
}

CZbxAlertSyncProcess::~CZbxAlertSyncProcess(void)
{
	Destroy();
}

CZbxAlertSyncProcess::CZbxAlertSyncProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	//======Read Config File======//
	m_strInfo = m_pConfigFile->ReadStringValue(ZBX_ALERT_SYNC_GROUP,INFOPATH);
	//=============================//
	Init();
	cout << "Init\n";
}


void CZbxAlertSyncProcess::Init()
{
	m_pAlertController = new CAlertController();
	m_pAlertSyncController = new CAlertSyncController();
	m_pAlertACKController = new CAlertACKController();
	m_pAlertSyncModel = new CAlertSyncModel();
	ControllerConnect();
	cout<<"Connected\n";
}

void CZbxAlertSyncProcess::Destroy()
{
	delete m_pAlertACKController;
	delete m_pAlertController;
	delete m_pAlertSyncController;
	delete m_pAlertSyncModel;
	delete m_pConfigFile;
}

ConnectInfo CZbxAlertSyncProcess::GetConnectInfo(string DBType)
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

bool CZbxAlertSyncProcess::ControllerConnect()
{
	//====================================MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	
	if(!m_pAlertACKController->Connect(CInfo))
		return false;
	if(!m_pAlertController->Connect(CInfo))
		return false;
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	
	return true;
}

bool CZbxAlertSyncProcess::Reconnect()
{
	if(!m_pAlertController->Reconnect())
		return false;
	if(!m_pAlertSyncController->Reconnect())
		return false;
	
	return true;
}

MA_RESULT CZbxAlertSyncProcess::ProcessSync()
{
	int iPos, iSeek, nAlertRecordNum;
	string strLog;
	//===========================Sync Alert================================
	while(true)
	{
		m_pAlertController->DestroyData();
		if(m_pAlertController->FindDB(QUERY(IS_SYNC<<0)))
		{
			while(true)
			{
				m_pAlertACKController->DestroyData();
				m_pAlertSyncController->DestroyData();
				m_pAlertSyncModel->DestroyData();
				if(!m_pAlertController->NextRecord())
				{
					m_pAlertController->DestroyData();
					break;
				}
				// CreateModel() Function is defined in child class
				CreateModel();
			}
		}
		else
			sleep(iParseSleepTime);
	}
	return MA_RESULT_SUCCESS;
}

int CZbxAlertSyncProcess::CreateModel()
{
	string strZbxDescription, strZbxKey, strZbxHost, strDept, strProd;
	string strSourceFrom, strSourceId, strAckMsg, strAlertMsg;
	long long lClock, lZbxTriggerId, lZbxItemId, lZbxHostId, lZbxEventId, lZbxServerId;
	int iMaintenance, iIsShow, iInternalStatus, iExternalStatus, iIsAck, iITSMIncId;
	int iZbxServerId, iZbxPriority, iPriority, iValueChanged;
	string strOldObjId, strNewObjId;
	BSONObj boChangeIsShow;
	try{
//========================================Get Alert Data==============================================
		strSourceId =  CUtilities::GetMongoObjId(m_pAlertController->GetStringResultVal(RECORD_ID));
		m_pAlertController->UpdateSynced(strSourceId);
		iPriority 		= atoi(m_pConfigFile->ReadStringValue(ZBX_ALERT_SYNC_GROUP,PRIORITY).c_str());
		iZbxPriority 	= m_pAlertController->GetLongResultVal(PRIORITY);
		strZbxHost 		= CUtilities::RemoveBraces(m_pAlertController->GetStringResultVal(HOST_NAME));
		iMaintenance 	= m_pAlertController->GetIntResultVal(MAINTENANCE);
		iValueChanged 	= m_pAlertController->GetIntResultVal(VALUE_CHANGED);
		if(iZbxPriority != iPriority || iValueChanged == 0)
			return 1;
		strZbxDescription 	= CUtilities::RemoveBraces(m_pAlertController->GetStringResultVal(DESCRIPTION));	
		iZbxServerId 		= m_pAlertController->GetIntResultVal(ZBX_SERVER_ID);
		lZbxServerId		= m_pAlertController->GetLongResultVal(SERVER_ID);
		lZbxHostId 			= m_pAlertController->GetLongResultVal(HOST_ID);
		lZbxItemId 			= m_pAlertController->GetLongResultVal(ITEM_ID);
		lZbxTriggerId 		= m_pAlertController->GetLongResultVal(TRIGGER_ID);
		strDept 			= CUtilities::RemoveBraces(m_pAlertController->GetStringResultVal(CMDB_DEPT_ALIAS));
		strProd 			= CUtilities::RemoveBraces(m_pAlertController->GetStringResultVal(CMDB_PROD_ALIAS));
		strZbxKey 			= CUtilities::RemoveBraces(m_pAlertController->GetStringResultVal(KEY_));
		lZbxEventId 		= m_pAlertController->GetLongResultVal(EVENT_ID);
		iIsShow 			= m_pAlertController->GetIntResultVal(STATUS);
		lClock 				= m_pAlertController->GetLongResultVal(CLOCK);
		
//================================Append Model===========================================	
		m_pAlertSyncModel->SetClock(lClock);
		m_pAlertSyncModel->SetIsShow(iIsShow);
		m_pAlertSyncModel->SetSourceFrom(ZBX_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetSourceId(strSourceId);
		m_pAlertSyncModel->SetDepartment(strDept);
		m_pAlertSyncModel->SetProduct(strProd);
		m_pAlertSyncModel->SetAlertMsg(strZbxDescription);
		m_pAlertSyncModel->SetZbxServerId(lZbxServerId);
		m_pAlertSyncModel->SetZbxZabbixServerId(iZbxServerId);
		m_pAlertSyncModel->SetZbxHostId(lZbxHostId);
		m_pAlertSyncModel->SetZbxHost(strZbxHost);
		m_pAlertSyncModel->SetZbxItemId(lZbxItemId);
		m_pAlertSyncModel->SetZbxKey(strZbxKey);
		m_pAlertSyncModel->SetZbxEventId(lZbxEventId);
		m_pAlertSyncModel->SetZbxTriggerId(lZbxTriggerId);
		m_pAlertSyncModel->SetZbxPriority(iZbxPriority);
		m_pAlertSyncModel->SetZbxDescription(strZbxDescription);
		m_pAlertSyncModel->SetZbxMaintenance(iMaintenance);
		//================ Get Old Alert ID =================
		if(m_pAlertSyncController->FindDB(Query(m_pAlertSyncModel->GetUniqueZbxAlertSyncBson()))){
			if(m_pAlertSyncController->NextRecord()){
				strOldObjId = CUtilities::GetMongoObjId(m_pAlertSyncController->GetStringResultVal(RECORD_ID));
			}
			m_pAlertSyncController->ChangeZbxIsShowState(m_pAlertSyncModel->GetUniqueZbxAlertSyncBson());
		}
		//==============================================
		m_pAlertSyncModel->PrepareRecord();
		m_pAlertSyncController->InsertDB(BSON(ZBX_ZBX_SERVER_ID<<iZbxServerId<<ZBX_EVENT_ID << lZbxEventId), m_pAlertSyncModel->GetRecordBson());
		//================ Get New Alert ID =================
		m_pAlertSyncController->DestroyData();
		if(m_pAlertSyncController->FindDB(Query(m_pAlertSyncModel->GetUniqueZbxAlertSyncBson()))){
			if(m_pAlertSyncController->NextRecord()){
				strNewObjId = CUtilities::GetMongoObjId(m_pAlertSyncController->GetStringResultVal(RECORD_ID));
			}
		}
		//=================Clone ACK====================
		if(!strOldObjId.empty() && !strNewObjId.empty()){
			if(m_pAlertACKController->FindDB(QUERY("alert_id"<<OID(strOldObjId))))
				m_pAlertACKController->CloneACK(strNewObjId);
		}
		//==============================================
	}
	catch(exception &ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return 1;
}