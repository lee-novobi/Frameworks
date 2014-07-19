#include "FunctionLogParser.h"
#include "../Controller/FunctionController.h"
#include "../Model/FunctionModel.h"
#include "../Config/LogParserData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

using namespace std;
CFunctionLogParser::CFunctionLogParser(void)
{
}

CFunctionLogParser::CFunctionLogParser(string strCfgFile)
{
	
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}

CFunctionLogParser::~CFunctionLogParser(void)
{
	Destroy();
}

void CFunctionLogParser::Init()
{
	string strLog, strInfo;

	//======Read Config File======//
	strLog = m_pConfigFile->ReadStringValue(FUNCTIONGROUP,LOGPATH);
	strInfo = m_pConfigFile->ReadStringValue(FUNCTIONGROUP,INFOPATH);
	m_bIsFunctionPart = false;
	if(m_pConfigFile->ReadStringValue(FUNCTIONGROUP,PARTITION).compare("true") == 0)
		m_bIsFunctionPart = true;
	//=============================//

	ControllerConnect();
	m_pDataObj = new CLogParserData(strLog.c_str(),strInfo.c_str());
}

void CFunctionLogParser::Destroy()
{
	delete m_pFunctionController;
	delete m_pFunctionModel;
	delete m_pDataObj;
}

ConnectInfo CFunctionLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}

bool CFunctionLogParser::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	m_pFunctionController = new CFunctionController();
	m_pFunctionModel = new CFunctionModel();

	if(!m_pFunctionController->Connect(CInfo))
		return false;
	return true;
}

void* CFunctionLogParser::PreParsing(int &iLength, int &nCurPosition)
{
	void *Buffer;
	Buffer = NULL;
	nCurPosition = m_pDataObj->GetPosition();
	if(nCurPosition == 0 && m_bIsFunctionPart == true)
	{
		m_pDataObj->SetNewLogFile();
		iLength = m_pDataObj->GetLength();
		Buffer = m_pDataObj->GetBuffer();
	}
	else
	{
		iLength = m_pDataObj->GetLength();
		if(nCurPosition < iLength)
		{
			Buffer = m_pDataObj->GetBuffer();
		}
		else if(m_bIsFunctionPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				Buffer = m_pDataObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					nCurPosition = 0;
					Buffer = m_pDataObj->GetBuffer();
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

MA_RESULT CFunctionLogParser::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	void* Buffer;
	int nCurPosition;
	int iLength;
	string strTemp;
	int iZbxServerId;
	long long lFunctionId, lTriggerId, lItemId;
	string strFunction, strParameter;
	
	Buffer = PreParsing(iLength, nCurPosition);
	
	//===========================Parse Log================================
	while(nCurPosition < iLength)
	{
		m_pFunctionController->DestroyData();
		m_pFunctionModel->DestroyData();

		iZbxServerId = 0;
		lFunctionId = lTriggerId = lItemId = 0;
		strFunction = strParameter = "";
		//Parse ServerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iZbxServerId = atoi(strTemp.c_str());
				m_pFunctionModel->SetZbxServerId(iZbxServerId);
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse lFunctionId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lFunctionId = atol(strTemp.c_str());
				m_pFunctionModel->SetFunctionId(lFunctionId);
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse lTriggerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lTriggerId = atol(strTemp.c_str());
				m_pFunctionModel->SetTriggerId(lTriggerId);
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse lItemId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lItemId = atol(strTemp.c_str());
				m_pFunctionModel->SetItemId(lItemId);
			}
			catch(exception &ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		
		//Parse strFunction
		strFunction = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		m_pFunctionModel->SetFunction(strFunction);
		
		//Parse strParameter
		strParameter = GetParameter((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		m_pFunctionModel->SetParameter(strParameter);

		//cout << iZbxServerId << " | " << lFunctionId << " | " << lTriggerId << " | " << lItemId << " | " << strFunction 
		//<< " | " << strParameter << endl; 
		m_pFunctionModel->PrepareRecord();
		m_pFunctionController->InsertDB(m_pFunctionModel->GetUniqueFunctionBson(), m_pFunctionModel->GetRecordBson());

		if(nCurPosition >= iLength)
		{
			stringstream strErrorMess;
			strErrorMess<<"\nFunction\nLength : "<<iLength<<endl;
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


MA_RESULT CFunctionLogParser::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}



