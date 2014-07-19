#include "WebHostCrossCheckProcess.h"

#include "../Controller/ZbxWebHostController.h"
#include "../Controller/HostWebController.h"

#include "../Model/HostWebModel.h"

#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

#include "mongo/client/dbclient.h"
using namespace mongo;

CWebHostCrossCheckProcess::CWebHostCrossCheckProcess(void)
{
}

CWebHostCrossCheckProcess::CWebHostCrossCheckProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	//======Read Config File======//
	// m_strInfo = m_pConfigFile->GetData(HOST_WEB_CROSS_CHECK_GROUP,INFOPATH);
	//=============================//
	Init();
}

CWebHostCrossCheckProcess::~CWebHostCrossCheckProcess(void)
{
}

void CWebHostCrossCheckProcess::Init()
{
	m_pHostWebController = new CHostWebController();
	m_pHostWebModel = new CHostWebModel();
	ControllerConnect();
}

void CWebHostCrossCheckProcess::Destroy()
{
	delete m_pZbxWebHostController;
	delete m_pHostWebController;
	delete m_pHostWebModel;
	delete m_pConfigFile;
}

ConnectInfo CWebHostCrossCheckProcess::GetConnectInfo(string DBType)
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

bool CWebHostCrossCheckProcess::ControllerConnect()
{
	//====================================MONGODB Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_ODA);
	
	if(!m_pHostWebController->Connect(CInfo))
		return false;
	//================================MYSQL Connection====================================
	CInfo = GetConnectInfo(MYSQL_WEB);
	
	m_pZbxWebHostController = new CZbxWebHostController(CInfo.strSource);
	if(!m_pZbxWebHostController->Connect(CInfo))
		return false;
	
	return true;
}

MA_RESULT CWebHostCrossCheckProcess::CrossCheckProcess()
{
	vector<long long> vZbxHostId;
	long long lZbxServerId, lHostWebServerId;
	string strHost, strName;
	
	while(true){
		m_pZbxWebHostController->ResetModel();
		if(m_pZbxWebHostController->FindDB()){
			m_pZbxWebHostController->GetFieldName();
			while(m_pZbxWebHostController->NextRow())
			{
				lZbxServerId = 0;
				strHost = strName = "";
				m_pHostWebController->DestroyData();
				m_pHostWebModel->DestroyData();
				lZbxServerId =  ((m_pZbxWebHostController->ModelGetLong(HOST_ID) - 10000) * 256) + WEB_ZBX_SERVER_ID;
				strHost = m_pZbxWebHostController->ModelGetString(HOST_NAME);
				strName = m_pZbxWebHostController->ModelGetString(NAME);
				m_pHostWebModel->SetServerId(lZbxServerId);
				m_pHostWebModel->SetHost(strHost);
				m_pHostWebModel->SetName(strName);
				m_pHostWebModel->PrepareRecord();
				m_pHostWebController->UpdateDB(Query(m_pHostWebModel->GetUniqueHostWebBson()), m_pHostWebModel->GetRecordBson());
				vZbxHostId.push_back(lZbxServerId);
				
			}
			
			m_pHostWebController->DestroyData();
			if(m_pHostWebController->FindDB())
			{
				while(m_pHostWebController->NextRecord())
				{
					lHostWebServerId = 0;
					m_pHostWebModel->DestroyData();
					lHostWebServerId = m_pHostWebController->GetLongResultVal(SERVER_ID);
					m_pHostWebModel->SetServerId(lHostWebServerId);
					for(int i = 0; i < vZbxHostId.size(); i++){
						if(vZbxHostId[i] == lHostWebServerId){
							m_pHostWebModel->SetDelete(0);
							break;
						}
						else if(i == vZbxHostId.size() - 1)
						{
							m_pHostWebModel->SetDelete(1);
						}
					}
					m_pHostWebModel->PrepareRecord();
					m_pHostWebController->UpdateDelete(Query(m_pHostWebModel->GetUniqueHostWebBson()), m_pHostWebModel->GetRecordBson());
				}
				m_pHostWebController->DestroyData();
			}
		}
		
		m_pZbxWebHostController->ResetModel();
		m_pHostWebController->DestroyData();
		m_pHostWebModel->DestroyData();
		vZbxHostId.clear();
		sleep(300);
	}
	
	return MA_RESULT_SUCCESS;
}