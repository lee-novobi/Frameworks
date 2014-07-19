#include "G8AlertSyncProcess.h"

#include "../Controller/AlertACKController.h"
#include "../Controller/G8AlertController.h"
#include "../Controller/AlertSyncController.h"
#include "../Controller/MapProductController.h"
#include "../Model/MongodbModel.h"
#include "../Model/AlertSyncModel.h"
#include "../Model/MapProductModel.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

#include "mongo/client/dbclient.h"
using namespace mongo;

CG8AlertSyncProcess::CG8AlertSyncProcess(void)
{
	
}

CG8AlertSyncProcess::CG8AlertSyncProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}

CG8AlertSyncProcess::~CG8AlertSyncProcess(void)
{
	Destroy();
}


void CG8AlertSyncProcess::Init()
{
	m_pAlertACKController = new CAlertACKController();
	m_pG8AlertController = new CG8AlertController();
	m_pAlertSyncController = new CAlertSyncController();
	m_pMapProductController = new CMapProductController();
	m_pAlertSyncModel = new CAlertSyncModel();
	m_pMapProductModel = new CMapProductModel();
	ControllerConnect();
}

void CG8AlertSyncProcess::Destroy()
{
	delete m_pAlertACKController;
	delete m_pG8AlertController;
	delete m_pAlertSyncController;
	delete m_pMapProductController;
	delete m_pAlertSyncModel;
	delete m_pMapProductModel;
	delete m_pConfigFile;
}

ConnectInfo CG8AlertSyncProcess::GetConnectInfo(string DBType)
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

bool CG8AlertSyncProcess::ControllerConnect()
{
	//====================================Mongo MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	
	if(!m_pAlertACKController->Connect(CInfo))
		return false;
	if(!m_pG8AlertController->Connect(CInfo))
		return false;
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	if(!m_pMapProductController->Connect(CInfo))
		return false;
	cout << "Connected" << endl;
	return true;
}

MA_RESULT CG8AlertSyncProcess::ProcessSync()
{
	string strOldObjId, strNewObjId;
	int iIsAcked;
	//===========================Sync Alert================================
	while(true)
	{
		m_pG8AlertController->DestroyData();
		m_pG8AlertController->FindDB(QUERY(OPERATION<<4));
		while(true)
		{
			strOldObjId = strNewObjId = "";
			if(!m_pG8AlertController->NextRecord())
			{
				m_pG8AlertController->DestroyData();
				break;
			}
			
			m_pAlertSyncController->DestroyData();
			m_pAlertACKController->DestroyData();
			m_pMapProductController->DestroyData();
			
			m_pAlertSyncModel->DestroyData();
			m_pMapProductModel->DestroyData();
			

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
			m_pG8AlertController->ResetOperation(m_pAlertSyncModel->GetUniqueAlertSyncBson());
		}
		sleep(10);
	}
	return MA_RESULT_SUCCESS;
}

bool CG8AlertSyncProcess::CreateModel()
{
	int iNumOfCase;
	long long lOutageStart;
	string strAlertMsg, strTicketId, strDescription, strSourceId, strTitle, strProduct, strAttachment, strAffectedDeals, strOutageStart, strItsmId;
	try{
//========================================Get Alert Data==============================================
		strSourceId 		= CUtilities::GetMongoObjId(m_pG8AlertController->GetStringResultVal(RECORD_ID));
		strAffectedDeals 	= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(AFFECTED_DEALS));
		strTitle 			= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(TITLE));
		strItsmId			= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(ITSM_ID));
		strDescription 		= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(DESCRIPTION)) + "_" + strAffectedDeals;
		if(!strItsmId.empty() && strItsmId.compare("EOO") != 0)
			strItsmId = "Incident " + strItsmId + " - ";
		else
			strItsmId = "";
		strProduct 			= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(PRODUCT));
		strTicketId 		= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(TICKET_ID));
		strAttachment		= CUtilities::RemoveBraces(m_pG8AlertController->GetStringResultVal(ATTACHMENTS));
		iNumOfCase 			= m_pG8AlertController->GetIntResultVal(NUM_OF_CASE);
		lOutageStart 		= m_pG8AlertController->GetLongResultVal(OUTAGE_START);
		//=====================Get ITSM Product=========================
		m_pMapProductModel->SetMapSource(G8_SOURCE_FROM_VAL);
		m_pMapProductModel->SetMapSourceProduct(strProduct);
		if(m_pMapProductController->FindDB(m_pMapProductModel->GetMapProductBySrcProductQuery()))
		{
			m_pMapProductController->NextRecord();
			strProduct = CUtilities::RemoveBraces(m_pMapProductController->GetStringResultVal(MAP_ITSM_PRODUCT));
		}
		if(!strProduct.empty() && strProduct.compare("EOO") != 0)
			strProduct = "[" + strProduct + "] ";
		else
			strProduct = "";
		strAlertMsg	= strItsmId + strProduct + strTitle + " " + strDescription;
//================================Append Model===========================================	
		m_pAlertSyncModel->SetIsShow(1);
		m_pAlertSyncModel->SetSourceFrom(G8_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetSourceId(strSourceId);
		m_pAlertSyncModel->SetTicketId(strTicketId);
		m_pAlertSyncModel->SetTitle(strTitle);
		m_pAlertSyncModel->SetDescription(strDescription);
		m_pAlertSyncModel->SetDepartment(G8_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetProduct(strProduct);
		m_pAlertSyncModel->SetAttactment(strAttachment);
		m_pAlertSyncModel->SetNumOfCase(iNumOfCase);
		m_pAlertSyncModel->SetItsmId(strItsmId);
		m_pAlertSyncModel->SetAlertMsg(strAlertMsg);
		m_pAlertSyncModel->SetClock(lOutageStart);
	}
	catch(exception &ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return true;
}