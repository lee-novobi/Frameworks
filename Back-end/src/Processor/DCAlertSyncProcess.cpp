#include "DCAlertSyncProcess.h"
#include "../Controller/AlertACKController.h"
#include "../Controller/DCAlertController.h"
#include "../Controller/AlertSyncController.h"
#include "../Model/MongodbModel.h"
#include "../Model/AlertSyncModel.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

#include "mongo/client/dbclient.h"
using namespace mongo;

CDCAlertSyncProcess::CDCAlertSyncProcess(void)
{
	
}

CDCAlertSyncProcess::CDCAlertSyncProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}

CDCAlertSyncProcess::~CDCAlertSyncProcess(void)
{
	Destroy();
}


void CDCAlertSyncProcess::Init()
{
	m_pAlertACKController = new CAlertACKController();
	m_pDCAlertController = new CDCAlertController();
	m_pAlertSyncController = new CAlertSyncController();
	m_pAlertSyncModel = new CAlertSyncModel();
	ControllerConnect();
}

void CDCAlertSyncProcess::Destroy()
{
	delete m_pAlertACKController;
	delete m_pDCAlertController;
	delete m_pAlertSyncController;
	delete m_pAlertSyncModel;
	delete m_pConfigFile;
}

ConnectInfo CDCAlertSyncProcess::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetData(DBType,HOST);
	CInfo.strUser = m_pConfigFile->GetData(DBType,USER);
	CInfo.strPass = m_pConfigFile->GetData(DBType,PASS);
	CInfo.strSource = m_pConfigFile->GetData(DBType,SRC);
	
	if(m_pConfigFile->GetData(DBType,PORT).compare("") != 0)
		CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetData(DBType,PORT);

	return CInfo;
}

bool CDCAlertSyncProcess::ControllerConnect()
{
	//====================================Mongo MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	
	if(!m_pAlertACKController->Connect(CInfo))
		return false;
	if(!m_pDCAlertController->Connect(CInfo))
		return false;
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	cout << "Connected" << endl;
	return true;
}

MA_RESULT CDCAlertSyncProcess::ProcessSync()
{
	string strOldObjId, strNewObjId;
	int iIsAcked;
	//===========================Sync Alert================================
	while(true)
	{
		m_pDCAlertController->DestroyData();
		m_pDCAlertController->FindDB(QUERY(IS_SYNC<<1));
		while(true)
		{
			strOldObjId = strNewObjId = "";
			if(!m_pDCAlertController->NextRecord())
			{
				m_pDCAlertController->DestroyData();
				break;
			}
			
			m_pAlertSyncController->DestroyData();
			m_pAlertACKController->DestroyData();
			
			m_pAlertSyncModel->DestroyData();
			

			if(!CreateModel())
				continue;
			//================ Get Old Alert ID =================
			if(m_pAlertSyncController->FindDB(Query(m_pAlertSyncModel->GetUniqueAlertSyncBson())))
				if(m_pAlertSyncController->NextRecord()){
					strOldObjId = CUtilities::GetMongoObjId(m_pAlertSyncController->GetStringResultVal(RECORD_ID));
				}
			//==============================================
			m_pAlertSyncController->ChangeIsShowState(m_pAlertSyncModel->GetUniqueAlertSyncBson());
			m_pAlertSyncModel->PrepareRecord();
			m_pAlertSyncController->InsertDB(BSONObj(), m_pAlertSyncModel->GetRecordBson());
			//================ Get New Alert ID =================
			m_pAlertSyncController->DestroyData();
			if(m_pAlertSyncController->FindDB(Query(m_pAlertSyncModel->GetUniqueAlertSyncBson())))
				if(m_pAlertSyncController->NextRecord()){
					strNewObjId = CUtilities::GetMongoObjId(m_pAlertSyncController->GetStringResultVal(RECORD_ID));
				}
			//==============================================
			if(!strOldObjId.empty() && !strNewObjId.empty()){
				if(m_pAlertACKController->FindDB(QUERY("alert_id"<<OID(strOldObjId))))
					m_pAlertACKController->CloneACK(strNewObjId);
			}
			m_pDCAlertController->ResetOperation(m_pAlertSyncModel->GetUniqueAlertSyncBson());
		}
		sleep(10);
	}
	return MA_RESULT_SUCCESS;
}

bool CDCAlertSyncProcess::CreateModel()
{
	int iNumOfCase;
	long long lOutageStart;
	string strStatus, strAlertMsg, strDescription, strSourceId, strTitle, strHostService;
	try{
//========================================Get Alert Data==============================================
		strSourceId 		= CUtilities::GetMongoObjId(m_pDCAlertController->GetStringResultVal(RECORD_ID));
		strStatus 			= CUtilities::RemoveBraces(m_pDCAlertController->GetStringResultVal(STATUS));
		strTitle 			= CUtilities::RemoveBraces(m_pDCAlertController->GetStringResultVal(DESCRIPTION));
		strHostService 		= CUtilities::RemoveBraces(m_pDCAlertController->GetStringResultVal("host_service"));
		strDescription 		= CUtilities::RemoveBraces(m_pDCAlertController->GetStringResultVal(DESCRIPTION)) + "_" + strHostService + "_" + strStatus;
		strAlertMsg	= strDescription;
//================================Append Model===========================================
		strStatus = CUtilities::ToUpperString(strStatus);
		if(strStatus.compare("OK") == 0)
			m_pAlertSyncModel->SetIsShow(0);
		else
			m_pAlertSyncModel->SetIsShow(1);
		
		m_pAlertSyncModel->SetSourceFrom(DC_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetSourceId(strSourceId);
		// m_pAlertSyncModel->SetPriority(strStatus);
		m_pAlertSyncModel->SetTitle(strTitle);
		m_pAlertSyncModel->SetDescription(strDescription);
		m_pAlertSyncModel->SetDepartment(DC_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetAlertMsg(strAlertMsg);
		m_pAlertSyncModel->SetZbxHost(strHostService);
		m_pAlertSyncModel->SetClock(atol(CUtilities::GetCurrTimeStamp().c_str()));
	}
	catch(exception &ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return true;
}