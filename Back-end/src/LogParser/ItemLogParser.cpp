#include "ItemLogParser.h"
#include "../Controller/ItemController.h"
#include "../Model/ItemModel.h"
#include "../Config/LogParserData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CItemLogParser::CItemLogParser(void)
{
}

CItemLogParser::CItemLogParser(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CItemLogParser::~CItemLogParser(void)
{
	Destroy();
}

void CItemLogParser::Init()
{
	string strLog, strInfo;

	//======Read Config File======//
	strLog = m_pConfigFile->ReadStringValue(ITEMGROUP,LOGPATH);
	strInfo = m_pConfigFile->ReadStringValue(ITEMGROUP,INFOPATH);
	m_bIsItemPart = false;
	if(m_pConfigFile->ReadStringValue(ITEMGROUP,PARTITION).compare("true") == 0)
		m_bIsItemPart = true;
	//=============================//
	m_pItemController = new CItemController();
	m_pItemModel = new CItemModel();
	m_pDataObj = new CLogParserData(strLog.c_str(),strInfo.c_str());
	ControllerConnect();
}

void CItemLogParser::Destroy()
{
	delete m_pItemController;
	delete m_pItemModel;
	delete m_pDataObj;
	delete m_pConfigFile;
}

ConnectInfo CItemLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	return CInfo;
}

bool CItemLogParser::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);

	if(!m_pItemController->Connect(CInfo))
		return false;
	return true;
}

char* CItemLogParser::PreParsing(int &iLength, int &nCurPosition)
{
	char* Buffer;
	Buffer = NULL;
	nCurPosition = m_pDataObj->GetPosition();
	if(nCurPosition == 0 && m_bIsItemPart == true)
	{
		m_pDataObj->SetNewLogFile();
		iLength = m_pDataObj->GetLength();
		Buffer = (char*)m_pDataObj->GetBuffer();
	}
	else
	{
		iLength = m_pDataObj->GetLength();
		if(nCurPosition < iLength)
		{
			Buffer = (char*)m_pDataObj->GetBuffer();
		}
		else if(m_bIsItemPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				Buffer = (char*)m_pDataObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					nCurPosition = 0;
					Buffer = (char*)m_pDataObj->GetBuffer();
				}
				else // sleep 1 second if nothing new
				{
					m_pDataObj->SetPosition(nCurPosition);
					sleep(iParseSleepTime);
				}
			}
		}
		else
			sleep(iParseSleepTime);
	}
	return Buffer;
}

MA_RESULT CItemLogParser::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	char* Buffer;
	int nCurPosition;
	int iLength;
	string strTemp;
	int iZbxServerId, iStatus, iValueType;
	long long lClock, lTriggerId, lHostId, lItemId, lServerId;
	string strDescription, strKey, strUnits;

	Buffer = (char*)PreParsing(iLength, nCurPosition);
	
	//===========================Parse Log================================
	while(nCurPosition < iLength)
	{
		m_pItemController->DestroyData();
		m_pItemModel->DestroyData();
		// Init database fields
		iZbxServerId = iStatus = 0;
		lClock = lItemId = lTriggerId = lHostId = lItemId = 0;
		strDescription = strKey = strUnits = "";
		//Parse Clock
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lClock = atol(strTemp.c_str());
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pItemModel->SetClock(lClock);
		
		//Parse ZbxServerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iZbxServerId = atoi(strTemp.c_str());
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pItemModel->SetZbxServerId(iZbxServerId);
		
		//Parse HostId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lHostId = atol(strTemp.c_str());
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pItemModel->SetHostId(lHostId);
		
		//ServerId
		lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
		m_pItemModel->SetServerId(lServerId);
		
		//Parse ItemId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lItemId = atol(strTemp.c_str());
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pItemModel->SetItemId(lItemId);
		
		//Parse Status
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iStatus = atoi(strTemp.c_str());
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pItemModel->SetStatus(iStatus);
		
		//Parse Key
		strKey = GetItemKey((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		m_pItemModel->SetKey(strKey);


		//Parse Description
		strTemp = GetDescription((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strDescription = strTemp;
		}
		m_pItemModel->SetDescription(strDescription);
		
		//Parse ValueType
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iValueType = atoi(strTemp.c_str());
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pItemModel->SetValueType(iValueType);

		//Parse Units
		//cout<< Buffer[nCurPosition] << endl;;
		if(Buffer[nCurPosition] != '\n')
		{
			strUnits = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		}
		m_pItemModel->SetUnits(strUnits);


		//cout << lClock << " | " << iZbxServerId << " | " << lHostId << " | " << lItemId << " | " << iStatus << " | " << strKey 
		//<< " | " << strDescription << " | " << iValueType << " | " <<endl; 
		m_pItemModel->PrepareRecord();
		m_pItemController->InsertDB(m_pItemModel->GetUniqueItemBson(), m_pItemModel->GetRecordBson());
		
		if(nCurPosition >= iLength)
		{
			stringstream strErrorMess;
			strErrorMess<<"\nItem\nLength : "<<iLength<<endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
			strErrorMess.str(string());
			strErrorMess<< "nCurPosition : " << nCurPosition << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
			m_pDataObj->SetPosition(iLength);
		}
	}

	m_pDataObj->ClearMapMem();
	return eResult;
}

MA_RESULT CItemLogParser::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}
