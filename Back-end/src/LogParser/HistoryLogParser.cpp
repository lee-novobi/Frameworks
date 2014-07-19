#include "HistoryLogParser.h"
#include "FileMapping.h"
#include "../Controller/MongodbController.h"
#include "../Model/MongodbModel.h"
#include "../Controller/ExternalAPIController.h"
#include "../Config/HistoryData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

CHistoryLogParser::CHistoryLogParser(void)
{
}

CHistoryLogParser::CHistoryLogParser(string strCfgFile)
{
}

CHistoryLogParser::~CHistoryLogParser(void)
{
}

MA_RESULT CHistoryLogParser::ProcessParse() 
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	while(true){
		eResult = ParseLog();
		if(eResult == MA_RESULT_FAIL)
			return eResult;
		sleep(10);
	}
	return eResult;
}

void CHistoryLogParser::Init()
{
	string strLog, strInfo, strPeriod;

	//======Read Config File======//
	strLog    = m_pConfigFile->ReadStringValue(HISTORYGROUP, LOGPATH);
	strInfo   = m_pConfigFile->ReadStringValue(HISTORYGROUP, INFOPATH);
	strPeriod = m_pConfigFile->ReadStringValue(HISTORYGROUP, PERIOD);

	////cout << "Log: " << strLog << endl;
	////cout << "Info: " << strInfo << endl;
	////cout << "Period: " << strPeriod << endl;
	
	m_pDataObj = new CHistoryData(strLog.c_str(), strInfo.c_str());
	m_pDataObj->SetPeriod(atoi(strPeriod.c_str()));
	ControllerConnect();
}

void CHistoryLogParser::Destroy()
{
	delete m_pDataObj;
	delete m_pConfigFile;
}

void CHistoryLogParser::ResetHostInfo(HostInfo& tagHostInfo)
{
	tagHostInfo.iMaintainance	= 0;
	tagHostInfo.iZbxServerId = 0;
	tagHostInfo.lClock = 0;
	tagHostInfo.lHostId = 0;
	tagHostInfo.lServerId = 0;
	tagHostInfo.strHost = "";
	tagHostInfo.strHostName = "";
	tagHostInfo.strSerialNumber = "";
	tagHostInfo.strZbIpAddress = "";
	tagHostInfo.strKey_ = "";
	tagHostInfo.strValue = "";
}

ConnectInfo CHistoryLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}


MA_RESULT CHistoryLogParser::ParseLog()
{	
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	char* pBuffer;
	int iPosition, iLength;
	string strTemp, strFileName, strDatetimeSuffix, strNextDatetimeSuffix, strPathDatePattern, strNextPathDatePattern, strProcessId, strSuffix;
	IntArray vtProcessId, vtPrevProcessId;
	//map<long long,string> mapHostMacDict;
	bool bFirstTime = true;
	bool bStop = false;
	CFileMapping objFileMapping;
	stringstream ssProcessId;
	HostInfo tagHostInfo;
	
	int iZbxServerId, iValueType, iMaintenance;
	long long lItemId, lClock, lHostId, lServerId;
	string strKey_, strValue, strHostName, strIfAddress;
	
	stringstream strLogMess;
	
	strDatetimeSuffix = "";
	
	while (!bStop) 
	{
		// cout << "vtProcessId.size(): " << vtProcessId.size() << endl;
		for (unsigned i=0; i<vtProcessId.size(); i++)
		{
			//======================Check for stopping========================//
			if(CheckStop())
				return MA_RESULT_FAIL;
			//================================================================//
			int iProcessId = vtProcessId[i];
			ssProcessId.str(string());
			ssProcessId << iProcessId;
			strProcessId = ssProcessId.str();
			
			strFileName = strPathDatePattern + strProcessId;
			
			objFileMapping.ClearMapMem();
			objFileMapping.ReadFile(strFileName);
			pBuffer = (char*)objFileMapping.GetBuffer();
			iLength = objFileMapping.GetLength();
			
			if (bFirstTime)
				iPosition = m_pDataObj->GetHistoryLogPosition(strProcessId);
			else 
				iPosition = 0; 
			
			while (iPosition < iLength) 
			{
					ResetHostInfo(tagHostInfo);
					// Init database fields
					lClock = iZbxServerId = lHostId = lItemId = iValueType	= lServerId = 0;
					strKey_ =  strValue = strHostName = strIfAddress = "";			

					//Parse Clock
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						try
						{									
							lClock = atol(strTemp.c_str());
							tagHostInfo.lClock = lClock;
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
					}
					
					//Parse ServerId
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						try
						{									
							iZbxServerId = atoi(strTemp.c_str());
							tagHostInfo.iZbxServerId = iZbxServerId;
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
					}
					
					//Parse HostId
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						try
						{									
							lHostId = atol(strTemp.c_str());
							tagHostInfo.lHostId = lHostId;
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
					}
					
					lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
					tagHostInfo.lServerId = lServerId;

					if(lServerId < 0){
						continue;
					}
					
					//Parse Hostname
					strTemp = GetBlock((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						strHostName = strTemp;
						strHostName = CUtilities::ReplaceBlockBracket(strHostName);
						tagHostInfo.strHost = strHostName;
						tagHostInfo.strHostName = strHostName;
					}

					//Parse zb IP Address
					strTemp = GetBlock((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						strIfAddress = strTemp;
						strIfAddress = CUtilities::ReplaceBlockBracket(strIfAddress);
						tagHostInfo.strZbIpAddress = strIfAddress;
					}

					//Parse Maintenance Status
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						try
						{									
							iMaintenance = atoi(strTemp.c_str());
							tagHostInfo.iMaintainance = iMaintenance;
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
					}

					//Parse ItemId
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						try
						{									
							lItemId = atol(strTemp.c_str());
							tagHostInfo.lItemId = lItemId;
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
					}
					
					//Parse Key_
					
					strKey_ = GetItemKey((const char*)pBuffer, (int&)iPosition, (int)iLength);
					tagHostInfo.strKey_ = strKey_;
					//Parse Value Type
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if(strTemp.compare("") != 0)
					{
						try
						{									
							iValueType = atoi(strTemp.c_str());
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
					}

					//Parse Value
					strTemp = GetItemValue((const char*)pBuffer, (int&)iPosition, (int)iLength);
					if (!strTemp.empty())
					{
						strValue = strTemp;
						tagHostInfo.strValue = strValue;
					}
					//cout << lClock << " | " << iZbxServerId << " | " << lHostId << " | " << lItemId << " | " << iValueType << " | " << strKey_ << endl;
					
					//===========================================Model Append===========================================================
					
					if(strKey_.compare(SYSTEM_INFO) == 0 || strKey_.compare(VB_SYSTEM_INFO) == 0)
					{
						try
						{
							GetValueBlock((const char*)pBuffer, (int&)iPosition, (int)iLength);
							//ParseSystemInfo((const char*)pBuffer, (int&)iPosition, (int)iLength, lServerId, mapHostMacDict, tagHostInfo);
							//ParseByKey();
						}
						catch(exception& ex)
						{
							strLogMess.str(string());
							strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
							CUtilities::WriteErrorLog(strLogMess.str());
						}
						//strTemp = GetValueBlock((const char*)pBuffer, (int&)iPosition, (int)iLength);
					}
					// else if (strKey_.compare(SDK_READ_TEXT) == 0)
					// {
						// if (strTemp.compare("") != 0)
						// {
							// strValue = strTemp;
							// tagHostInfo.strSerialNumber = strValue;
							// tagHostInfo.strSerialNumber.erase(std::find_if(tagHostInfo.strSerialNumber.rbegin(), tagHostInfo.strSerialNumber.rend(), std::not1(std::ptr_fun<int, int>(std::isspace))).base(), tagHostInfo.strSerialNumber.end());
							// m_pParserController->ModelAppend(paraServerKey, tagHostInfo.strSerialNumber);
							// m_pParserController->ModelAppend(paraLastClock, tagHostInfo.lClock);
							// m_pParserController->Update();
						// }
					// }
					else if(strKey_.find(WEB_TEST) != std::string::npos)
					{	
						ParseWebInfo(tagHostInfo, strSuffix);
					}
					// cout << lClock << " | " << strHostName << endl;
					//cout << lClock << " | " << iZbxServerId << " | " << lHostId << " | " << strHostName << " | " << strIfAddress << " | " 
					//<< iMaintenance << " | " << lItemId << " | " << strKey_ << " | " << iValueType << " | " << strValue << endl;
			}
			objFileMapping.ClearMapMem();
			m_pDataObj->SetProcessPosition(strProcessId, iPosition); 
			
		}
		if (vtProcessId.size() > 0) 
		{
			bFirstTime = false;
			strDatetimeSuffix = m_pDataObj->GetNextDatetime(strDatetimeSuffix);
		}
		else
			strDatetimeSuffix = m_pDataObj->GetLastDatetime();
			
		// cout << "strDatetimeSuffix: " << strDatetimeSuffix << endl;
		strPathDatePattern = m_pDataObj->GetPathDatePatternHistory(strDatetimeSuffix);
		// cout << "strPathDatePattern: " << strPathDatePattern << endl;
		vtProcessId.clear();
		vtProcessId = CUtilities::GetListZabbixProcessId(strPathDatePattern);
		if (vtProcessId.size() > 0)
		{	
			bStop = false;
			m_pDataObj->SetLastDatetime(strDatetimeSuffix);
			if(vtPrevProcessId.size() != vtProcessId.size()){
				strLogMess.str(string());
				strLogMess << strPathDatePattern << "( ";
							for(unsigned i=0; i<vtProcessId.size(); i++)
								strLogMess << vtProcessId[i] << " ";
							strLogMess << " )" <<  endl;
				CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, PROCESS_NAME, "CHistoryLogParser::ParseLog():ProcChangeSize", strLogMess.str()));
				
				if(!vtPrevProcessId.empty())
				{
					unsigned k = 0;
					for(unsigned i=0; i<vtPrevProcessId.size(); i++)
						for(unsigned j=k; j<vtProcessId.size(); j++)
						{
							if(vtPrevProcessId[i] == vtProcessId[j])
							{
								k = j;
								break;
							}
							else if(j == vtProcessId.size() - 1)
							{
								stringstream streamProcessId;
								streamProcessId << vtPrevProcessId[i];
								m_pDataObj->SetProcessPosition(streamProcessId.str(), 0);
							}
						}
				}
			}
			vtPrevProcessId.clear();
			vtPrevProcessId = vtProcessId;
		}
		else 
		{
			bStop = true;
		}
		
	}
	return eResult;
}

IP_TYPE CHistoryLogParser::GetIPType(string strIP)
{
	struct sockaddr_in antelope;
	unsigned long iStartPrivateIPClassA, iEndPrivateIPClassA, iStartPrivateIPClassB, 
	iEndPrivateIPClassB, iStartPrivateIPClassC, iEndPrivateIPClassC, iStartPrivateIPLocal, iEndPrivateIPLocal, iInputIP;
	
	iStartPrivateIPClassA		= CUtilities::IpToLong("10.0.0.0");
	iEndPrivateIPClassA			= CUtilities::IpToLong("10.255.255.255");
	
	iStartPrivateIPClassB		= CUtilities::IpToLong("172.16.0.0");
	iEndPrivateIPClassB			= CUtilities::IpToLong("172.31.255.255");

	iStartPrivateIPClassC		= CUtilities::IpToLong("192.168.0.0");
	iEndPrivateIPClassC			= CUtilities::IpToLong("192.168.255.255");
	
	iStartPrivateIPLocal		= CUtilities::IpToLong("127.0.0.0");
	iEndPrivateIPLocal			= CUtilities::IpToLong("127.255.255.255");
	
	iInputIP					= CUtilities::IpToLong(strIP);
	
	if(iInputIP >= iStartPrivateIPClassA && iInputIP <= iEndPrivateIPClassA)
		return IP_PRIVATE;
	if(iInputIP >= iStartPrivateIPClassB && iInputIP <= iEndPrivateIPClassB)
		return IP_PRIVATE;
	if(iInputIP >= iStartPrivateIPClassC && iInputIP <= iEndPrivateIPClassC)
		return IP_PRIVATE;
	if(iInputIP >= iStartPrivateIPLocal && iInputIP <= iEndPrivateIPLocal)
		return IP_PRIVATE;
	return IP_PUBLIC;
}

bool CHistoryLogParser::CheckStop()
{
	int iCheckStop = 1;
	try{
		iCheckStop = atoi(m_pConfigFile->ReadStringValue(HISTORYGROUP, STOPPARSING).c_str());
	}
	catch(exception& ex)
	{
		stringstream strLogMess;
		strLogMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strLogMess.str());
	}
	if(iCheckStop == 1)
		return true;
	return false;
}
