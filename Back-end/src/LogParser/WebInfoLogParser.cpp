#include "WebInfoLogParser.h"
#include "FileMapping.h"
#include "../Config/ConfigFileParse.h"
#include "../Controller/WebInfoController.h"
#include "../Model/WebInfoModel.h"
#include "../Controller/WebInfoHistoryController.h"
#include "../Model/WebInfoHistoryModel.h"
#include "../Common/DBCommon.h"
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

CWebInfoLogParser::CWebInfoLogParser(void)
{
}

CWebInfoLogParser::CWebInfoLogParser(string strCfgFile)
{
	m_pWebInfoController = new CWebInfoController();
	m_pWebInfoModel = new CWebInfoModel();
	m_pWebInfoHistoryController = new CWebInfoHistoryController();
	m_pWebInfoHistoryModel = new CWebInfoHistoryModel();
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
	cout << "Init\n";
}


CWebInfoLogParser::~CWebInfoLogParser(void)
{
	Destroy();
	delete m_pWebInfoController;
	delete m_pWebInfoModel;
	delete m_pWebInfoHistoryController;
	delete m_pWebInfoHistoryModel;
}


bool CWebInfoLogParser::ControllerConnect()
{
	bool bResult = false;
	ConnectInfo CInfo = GetConnectInfo(MONGODB_ODA);
	bResult = m_pWebInfoController->Connect(CInfo);
	bResult = m_pWebInfoHistoryController->Connect(CInfo);
	return bResult;
}

void CWebInfoLogParser::ParseWebInfo(const HostInfo& tagHostInfo, string &strSuffix)
{
	bool bIsFound;
	string strUnit, strWebKey, strAppName, strPrevValue, strNewSuffix, strStepName;
	BSONObj bsonWebInfoCondition, bsonWebInfoRecord, bsonWebInfoHistoryRecord, bsonKeysIndex;
	
	m_pWebInfoController->DestroyData();
	m_pWebInfoModel->DestroyData();
	m_pWebInfoHistoryController->DestroyData();
	m_pWebInfoHistoryModel->DestroyData();
	
	strStepName = CUtilities::GetStepNameByWebKey(tagHostInfo.strKey_);
	strAppName = CUtilities::GetNameByWebKey(tagHostInfo.strKey_);
	strUnit = CUtilities::GetUnitByWebKey(tagHostInfo.strKey_);
	
	m_pWebInfoModel->SetStepName(strStepName);
	m_pWebInfoModel->SetAppName(strAppName);
	m_pWebInfoModel->SetWebKey(tagHostInfo.strKey_);
	m_pWebInfoModel->SetServerId(tagHostInfo.lServerId);
	m_pWebInfoModel->SetClock(tagHostInfo.lClock);
	m_pWebInfoModel->SetZbxServerId(tagHostInfo.iZbxServerId);
	m_pWebInfoModel->SetHostName(tagHostInfo.strHostName);
	m_pWebInfoModel->SetHostId(tagHostInfo.lHostId);
	m_pWebInfoModel->SetItemId(tagHostInfo.lItemId);
	m_pWebInfoModel->SetUnit(strUnit);
	bsonWebInfoCondition = m_pWebInfoModel->GetUniqueWebInfoBson();
	
	m_pWebInfoHistoryModel->SetAppName(strAppName);
	m_pWebInfoHistoryModel->SetStepName(strStepName);
	m_pWebInfoHistoryModel->SetWebKey(tagHostInfo.strKey_);
	m_pWebInfoHistoryModel->SetServerId(tagHostInfo.lServerId);
	m_pWebInfoHistoryModel->SetClock(tagHostInfo.lClock);
	m_pWebInfoHistoryModel->SetZbxServerId(tagHostInfo.iZbxServerId);
	m_pWebInfoHistoryModel->SetHostName(tagHostInfo.strHostName);
	m_pWebInfoHistoryModel->SetHostId(tagHostInfo.lHostId);
	m_pWebInfoHistoryModel->SetItemId(tagHostInfo.lItemId);
	m_pWebInfoHistoryModel->SetUnit(strUnit);
	
	strNewSuffix = CUtilities::GetSuffixPartition(tagHostInfo.lClock,atoi(m_pConfigFile->ReadStringValue(HISTORYGROUP, PARTITION_DAY).c_str()));
	bsonKeysIndex = BSONObj();
	if(strNewSuffix.compare(strSuffix) != 0)
	{
		strSuffix = strNewSuffix;
		bsonKeysIndex = m_pWebInfoHistoryModel->GetKeysIndex();
	}
	// cout << "strNewSuffix : "  << strNewSuffix << endl;
	bIsFound = m_pWebInfoController->FindDB(Query(bsonWebInfoCondition)); // Find Prev Record
	if(bIsFound){
		m_pWebInfoController->NextRecord();
		strPrevValue = m_pWebInfoController->GetStringResultVal(LAST_VALUE);
	}
	if(tagHostInfo.strKey_.find(WEB_TEST_IN) != std::string::npos)
	{
		if(tagHostInfo.strKey_.find(",,") != std::string::npos ){
			// cout << "WEB_TEST_IN Step" << endl;
			m_pWebInfoModel->SetStepSpeed(atof(tagHostInfo.strValue.c_str()));
			if(bIsFound){
				m_pWebInfoModel->SetPreStepSpeed(atof(strPrevValue.c_str())); // Set Last Value to Prev Value
			}
			m_pWebInfoHistoryModel->SetStepSpeed(atof(tagHostInfo.strValue.c_str()));
		}
		else
		{
			// cout << "WEB_TEST_IN" << endl;
			m_pWebInfoModel->SetSpeed(atof(tagHostInfo.strValue.c_str()));
			if(bIsFound){
				m_pWebInfoModel->SetPreSpeed(atof(strPrevValue.c_str()));// Set Last Value to Prev Value
			}
			m_pWebInfoHistoryModel->SetSpeed(atof(tagHostInfo.strValue.c_str()));
		}
	}
	else if(tagHostInfo.strKey_.find(WEB_TEST_FAIL) != std::string::npos)
	{
		// cout << "WEB_TEST_FAIL" << endl;
		m_pWebInfoModel->SetFail(atoi(tagHostInfo.strValue.c_str()));
		if(bIsFound){
			m_pWebInfoModel->SetPreFail(atoi(strPrevValue.c_str()));// Set Last Value to Prev Value
		}
		m_pWebInfoHistoryModel->SetFail(atoi(tagHostInfo.strValue.c_str()));
	}
	else if(tagHostInfo.strKey_.find(WEB_TEST_TIME) != std::string::npos)
	{
		// cout << "WEB_TEST_TIME" << endl;
		m_pWebInfoModel->SetResTime(atof(tagHostInfo.strValue.c_str()));
		if(bIsFound){
			m_pWebInfoModel->SetPreResTime(atof(strPrevValue.c_str()));// Set Last Value to Prev Value
		}
		m_pWebInfoHistoryModel->SetResTime(atof(tagHostInfo.strValue.c_str()));
	}
	else if(tagHostInfo.strKey_.find(WEB_TEST_RSPCODE) != std::string::npos)
	{
		// cout << "WEB_TEST_RSPCODE" << endl;
		m_pWebInfoModel->SetResCode(atoi(tagHostInfo.strValue.c_str()));
		if(bIsFound){
			m_pWebInfoModel->SetPreResCode(atoi(strPrevValue.c_str()));// Set Last Value to Prev Value
		}
		m_pWebInfoHistoryModel->SetResCode(atoi(tagHostInfo.strValue.c_str()));
	}
	else if(tagHostInfo.strKey_.find(WEB_TEST_ERROR) != std::string::npos)
	{
		// cout << "WEB_TEST_ERROR" << endl;
		m_pWebInfoModel->SetErrMsg(tagHostInfo.strValue.c_str());
		if(bIsFound){
			m_pWebInfoModel->SetPreErrMsg(CUtilities::RemoveBraces(strPrevValue.c_str()));// Set Last Value to Prev Value
		}
		m_pWebInfoHistoryModel->SetErrMsg(tagHostInfo.strValue.c_str());
	}
	// cout << tagHostInfo.lServerId << " | " << tagHostInfo.iZbxServerId << " | " << tagHostInfo.lHostId 
				// << " | " << tagHostInfo.strKey_ << " | " << tagHostInfo.strValue << endl;
				
	m_pWebInfoModel->PrepareRecord();
	m_pWebInfoHistoryModel->PrepareRecord();
	
	bsonWebInfoRecord = m_pWebInfoModel->GetRecordBson();
	bsonWebInfoHistoryRecord = m_pWebInfoHistoryModel->GetRecordBson();
	
	m_pWebInfoController->InsertDB(bsonWebInfoCondition, bsonWebInfoRecord);
	m_pWebInfoHistoryController->InsertDBPartition(BSONObj(), bsonWebInfoHistoryRecord, bsonKeysIndex, strNewSuffix);
	
	m_pWebInfoController->DestroyData();
	m_pWebInfoModel->DestroyData();
	m_pWebInfoHistoryController->DestroyData();
	m_pWebInfoHistoryModel->DestroyData();
	bsonWebInfoCondition = bsonWebInfoRecord = bsonWebInfoHistoryRecord = bsonKeysIndex = BSONObj();
}



