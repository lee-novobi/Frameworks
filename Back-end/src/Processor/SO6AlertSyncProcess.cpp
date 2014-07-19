#include "SO6AlertSyncProcess.h"

#include "../Controller/SO6AlertController.h"
#include "../Controller/AlertSyncController.h"
#include "../Controller/ImpactLevelController.h"
#include "../Controller/MapProductController.h"

#include "../Model/MongodbModel.h"
#include "../Model/AlertSyncModel.h"
#include "../Model/ImpactLevelModel.h"
#include "../Model/MapProductModel.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

#include "mongo/client/dbclient.h"
using namespace mongo;

CSO6AlertSyncProcess::CSO6AlertSyncProcess(void)
{
}

CSO6AlertSyncProcess::CSO6AlertSyncProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	//=============================//
	Init();
}

CSO6AlertSyncProcess::~CSO6AlertSyncProcess(void)
{
	Destroy();
}

void CSO6AlertSyncProcess::Init()
{
	m_pSO6AlertController = new CSO6AlertController();
	m_pAlertSyncController = new CAlertSyncController();
	m_pImpactLevelController = new CImpactLevelController();
	m_pMapProductController = new CMapProductController();
	m_pAlertSyncModel = new CAlertSyncModel();
	m_pImpactLevelModel = new CImpactLevelModel();
	m_pMapProductModel = new CMapProductModel();
	ControllerConnect();
}

void CSO6AlertSyncProcess::Destroy()
{
	delete m_pSO6AlertController;
	delete m_pAlertSyncController;
	delete m_pImpactLevelController;
	delete m_pMapProductController;
	delete m_pAlertSyncModel;
	delete m_pImpactLevelModel;
	delete m_pMapProductModel;
	delete m_pConfigFile;
}

ConnectInfo CSO6AlertSyncProcess::GetConnectInfo(string DBType)
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

bool CSO6AlertSyncProcess::ControllerConnect()
{
	//====================================Mongo MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	if(!m_pImpactLevelController->Connect(CInfo))
		return false;
	if(!m_pMapProductController->Connect(CInfo))
		return false;
	//==================================Mysql SO6 Connection===================================
	CInfo = GetConnectInfo(MYSQL_SO6);
	if(!m_pSO6AlertController->Connect(CInfo))
		return false;
	return true;
}

bool CSO6AlertSyncProcess::Reconnect()
{
	if(!m_pSO6AlertController->Reconnect())
		return false;
	// if(!m_pAlertSyncController->Reconnect())
		// return false;
	// if(!m_pImpactLevelController->Reconnect())
		// return false;
	// if(!m_pMapProductController->Reconnect())
		// return false;
	return true;
}

MA_RESULT CSO6AlertSyncProcess::ProcessSync()
{
	vector<string> vStrAlertId;
	//===========================Sync Alert================================
	while(true)
	{
		while(!Reconnect())
			sleep(5);
		vStrAlertId.clear();
		m_pSO6AlertController->ResetModel();
		m_pSO6AlertController->FindDB();
		m_pSO6AlertController->GetFieldName();
		//===Hieutt Test===
		// int i = 0;
		// int n = 200;
		// while(i <= n)
		//============
		while(true)
		{
			//===Hieutt Test===
			// i++;
			//============
			m_pAlertSyncController->DestroyData();
			m_pImpactLevelController->DestroyData();
			m_pMapProductController->DestroyData();
			
			m_pAlertSyncModel->DestroyData();
			m_pImpactLevelModel->DestroyData();
			m_pMapProductModel->DestroyData();
			
			if(!m_pSO6AlertController->NextRow())
			{
				m_pSO6AlertController->ResetModel();
				break;
			}
			vStrAlertId.push_back(m_pSO6AlertController->ModelGetString("id"));

			if(!CreateModel())
				continue;
			m_pAlertSyncModel->PrepareRecord();
			m_pAlertSyncController->InsertDB(m_pAlertSyncModel->GetUniqueAlertSyncBson(), m_pAlertSyncModel->GetRecordBson());
		}
		m_pAlertSyncController->HideAlertNotInSrcId(SO6_SOURCE_FROM_VAL, vStrAlertId);
		usleep(200);
		if(vStrAlertId.empty())
			usleep(800);
	}
	return MA_RESULT_SUCCESS;
}

string CSO6AlertSyncProcess::GetSO6Product(string strServerName)
{
	int iFindStart, iFindEnd, iSpec;
	string strRes;
	strRes = "";
	if(strServerName.compare("SO6_G6Mobile_XH-Report21") == 0)
	{
		strRes = "XH";
		return strRes;
	}
	if(strServerName.compare("SO6_G6Membase_Mem45-UNIN") == 0)
	{
		strRes = "UNIN";
		return strRes;
	}
	// iSpec = strServerName.find("SO6_");
	// if(iSpec != std::string::npos)
	// {
		// iFindEnd = strServerName.find("_",4);
		// strRes = strServerName.substr(iSpec,iFindEnd);
		// return strRes;
	// }
	iSpec = strServerName.find("_DEV");
	if(iSpec == std::string::npos) 
	{
		iSpec = strServerName.find("_Dev");
	}
	if(iSpec != std::string::npos )
	{
		iSpec+=4;
		strRes = strServerName.substr(0,iSpec);
		return strRes;
	}
	
	iFindStart = strServerName.find("_");
	if(iFindStart == std::string::npos)
	{
		iFindStart = strServerName.find("-");
	}
	if(iFindStart != std::string::npos)
	{
		iFindEnd = strServerName.find("_", iFindStart + 1);
		if(iFindEnd != std::string::npos)
		{
			strRes = strServerName.substr(iFindStart+1,iFindEnd - iFindStart - 1);
		}
		else
		{
			iFindEnd = strServerName.find("-", iFindStart + 1);
			if(iFindEnd != std::string::npos)
				strRes = strServerName.substr(iFindStart+1,iFindEnd - iFindStart - 1);
		}
	}
	return strRes;
}

bool CSO6AlertSyncProcess::CreateModel()
{
	int iNumOfCase, iImpactLevel, iPriority;
	string strDescription, strSourceId, strTitle, strProduct, strAttachment, strAffectedDeals, strOutageStart, 
	strServerName, strContent, strMsgAlert, strPriority, strTmp;
	long long lClock;
	vector<string> vInformation;
	vector<string> vInformationSystem;
	vector<string> vArrIp;
	vector<string> vArrProduct;
	iImpactLevel = NULL;
	try{
//========================================Get Alert Data==============================================
		strPriority = m_pSO6AlertController->ModelGetString(PRIORITY);
		strTmp = CUtilities::ToLowerString(strPriority);
		if(strTmp.compare("notmonitor") == 0 || strTmp.compare("low") == 0)
			return false;
		strSourceId = m_pSO6AlertController->ModelGetString("id");
		lClock = CUtilities::UnixTimeFromString(m_pSO6AlertController->ModelGetString("updatetime"));
		strServerName = m_pSO6AlertController->ModelGetString(SERVERNAME);
		//============Map Product=============
		strProduct = GetSO6Product(strServerName);
		m_pMapProductModel->SetMapSource(SO6_SOURCE_FROM_VAL);
		m_pMapProductModel->SetMapSourceProduct(strProduct);
		if(m_pMapProductController->FindDB(m_pMapProductModel->GetMapProductBySrcProductQuery()))
		{
			m_pMapProductController->NextRecord();
			strProduct = CUtilities::RemoveBraces(m_pMapProductController->GetStringResultVal(MAP_ITSM_PRODUCT));
		}
		//====================================
		strContent = m_pSO6AlertController->ModelGetString(CONTENT);
		strContent = CUtilities::StripTags(strContent);
		strContent = CUtilities::ReplaceString(strContent,"\r\n","\n");
		strContent = CUtilities::ReplaceString(strContent,"\n\r","\n");
		strDescription = strContent;
		vInformation = CUtilities::SplitString(strContent,"\n");
		if(vInformation.size() > 1)
		{
			strTmp = vInformation[1];
			std::remove(strTmp.begin(), strTmp.end(), ' ');
			if(!strTmp.empty())
				strMsgAlert = CUtilities::ReplaceString(vInformation[1],",","\n");
			else
				strMsgAlert = CUtilities::ReplaceString(vInformation[0],",","\n");
		}
		else
			strMsgAlert = CUtilities::ReplaceString(vInformation[0],",","\n");
		strMsgAlert = "[" + strProduct + "] " + strMsgAlert;
		//=======Hieutt Test========
		// cout << strMsgAlert << endl;
		// cout << strSourceId << endl;
		//==========================
//================================Append Model===========================================	
		m_pAlertSyncModel->SetClock(lClock);
		m_pAlertSyncModel->SetIsShow(1);
		m_pAlertSyncModel->SetSourceId(strSourceId);
		m_pAlertSyncModel->SetSourceFrom(SO6_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetDescription(strDescription);
		m_pAlertSyncModel->SetDepartment(SO6_SOURCE_FROM_VAL);
		m_pAlertSyncModel->SetAlertMsg(strMsgAlert);
		m_pAlertSyncModel->SetPriority(strPriority);
		m_pAlertSyncModel->SetZbxHost(strServerName);
		//=======Hieutt Test========
		m_pAlertSyncModel->SetProduct(strProduct);
		//==========================
	}
	catch(exception &ex)
	{
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return true;
}