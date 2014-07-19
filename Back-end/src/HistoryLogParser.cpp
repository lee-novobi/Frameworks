#include "HistoryLogParser.h"
#include "../Controller/HostController.h"
#include "../Config/HistoryConfig.h"
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
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CHistoryLogParser::~CHistoryLogParser(void)
{
	Destroy();
}

MA_RESULT CHistoryLogParser::ProcessParse() 
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	eResult = ParseLog();
	return eResult;
}

void CHistoryLogParser::Init()
{
	string strLog, strInfo, strPeriod;

	//======Read Config File======//
	strLog    = m_pConfigFile->GetData(HISTORYGROUP, LOGPATH);
	strInfo   = m_pConfigFile->GetData(HISTORYGROUP, INFOPATH);
	strPeriod = m_pConfigFile->GetData(HISTORYGROUP, PERIOD);

	cout << "Log: " << strLog << endl;
	cout << "Info: " << strInfo << endl;
	cout << "Period: " << strPeriod << endl;

	ControllerConnect();
	m_pConfigObj = new CHistoryConfig(strLog.c_str(), strInfo.c_str());
	m_pConfigObj->SetPeriod(atoi(strPeriod.c_str()));
}

void CHistoryLogParser::Destroy()
{
	delete m_pActiveHostController;
	delete m_pConfigObj;
	delete m_pConfigFile;
}

ConnectInfo CHistoryLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetData(DBType,HOST);
	CInfo.strUser = m_pConfigFile->GetData(DBType,USER);
	CInfo.strPass = m_pConfigFile->GetData(DBType,PASS);
	CInfo.strSource = m_pConfigFile->GetData(DBType,SRC);
	return CInfo;
}

bool CHistoryLogParser::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MYSQL);
	m_pActiveHostController = new CHostController(CInfo.strSource);

	if(!m_pActiveHostController->Connect(CInfo))
		return false;
	return true;
}

char* CHistoryLogParser::PreParsing(int &iLength, int &nCurPosition)
{
	char* Buffer;
	Buffer = NULL;
	nCurPosition = m_pConfigObj->GetPosition();
	//cout << "Position: " << nCurPosition << endl;
	if(nCurPosition == 0 && m_bIsHostPart == true)
	{
		m_pConfigObj->SetNewLogFile();
		iLength = m_pConfigObj->GetLength();
		Buffer = (char*)m_pConfigObj->GetBuffer();
	}
	else
	{
		iLength = m_pConfigObj->GetLength();
		if(nCurPosition < iLength)
		{
			Buffer = (char*)m_pConfigObj->GetBuffer();
		}
		else if(m_bIsHostPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pConfigObj->SetNewLogFile();
				iLength = m_pConfigObj->GetLength();
				Buffer = (char*)m_pConfigObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				m_pConfigObj->SetNewLogFile();
				iLength = m_pConfigObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					nCurPosition = 0;
					Buffer = (char*)m_pConfigObj->GetBuffer();
				}
				else // sleep 1 second if nothing new
				{
					m_pConfigObj->SetPosition(nCurPosition);
					sleep(iParseSleepTime);
				}
			}
		}
		else
			sleep(iParseSleepTime);
	}
	return Buffer;
}

MA_RESULT CHistoryLogParser::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	char* pBuffer;
	int nCurPosition;
	int iLength;
	string strTemp, strDatetimeSuffix, strPathDatePattern;
	IntArray vtProcessId;
	int iClock, iServerId, iHostId, iItemId, iValueType, iMaintenance;
	long long lHostId;
	string strKey_, strValue;

	string strHostname, strIfAddress;

	string strFileTail, strFileName;
	int iPosition;

	//Buffer = (char*)PreParsing(iLength, nCurPosition);
	strDatetimeSuffix = m_pConfigObj->GetLastDatetime();

	strPathDatePattern =  m_pConfigObj->GetPathDatePatternHistory(strDatetimeSuffix);
	vtProcessId = CUtilities::GetListZabbixProcessId(strPathDatePattern);

	for (unsigned i=0; i< vtProcessId.size(); i++)
	{
		  int iProcessId = vtProcessId[i];

		  stringstream ssProcessId;
		  string strProcessId;
		  ssProcessId << iProcessId;
		  strProcessId = ssProcessId.str();

		  //strFileTail = m_pConfigObj->GetHistoryLogTail(strProcessId);
		  iPosition = m_pConfigObj->GetHistoryLogPosition(strProcessId);

		 // cout << "File tail: " << strFileTail << endl;
		 // cout << "File position: " << iPosition << endl;
		/*  if (strFileTail == "") 
		  {
			  strFileTail = strDatetimeSuffix + "_" + strProcessId; 
		  } */
		  strFileName = strPathDatePattern + strProcessId;
		 
		  iLength = m_pConfigObj->GetFileLength(strFileName);
		  pBuffer = (char*)m_pConfigObj->ReadFileBuffer(strFileName);
		 
		  while (iPosition < iLength) 
		  {
				// Init database fields
				iClock = iServerId = iHostId = iItemId = iValueType	= 0;
				strKey_ =  strValue = "";

				//Parse Clock
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					try
					{									
						iClock = atoi(strTemp.c_str());
					}
					catch(char *str)
					{
						cout << str;
						continue;
					}
				}
				//cout << "Clock: " << iClock << endl;

				//Parse ServerId
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					try
					{									
						iServerId = atoi(strTemp.c_str());
					}
					catch(char *str)
					{
						cout << str;
						continue;
					}
				}
				//cout << "ServerId: " << iServerId << endl;

				//Parse HostId
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					try
					{									
						lHostId = atol(strTemp.c_str());
					}
					catch(char *str)
					{
						cout << str;
						continue;
					}
				}
				//cout << "HostId: " << lHostId << endl;

				//Parse Hostname
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					strHostname = strTemp;
				}
				//cout << "Hostname: " << strHostname << endl;
				

				//Parse Interface
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					strIfAddress = strTemp;
				}
				//cout << "IfAddress: " << strIfAddress << endl;

				//Parse Maintenance Status
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					try
					{									
						iMaintenance = atoi(strTemp.c_str());
					}
					catch(char *str)
					{
						cout << str;
						continue;
					}
				}
				//cout << "Maintenance: " << iMaintenance << endl;

				//Parse ItemId
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					try
					{									
						iItemId = atol(strTemp.c_str());
					}
					catch(char *str)
					{
						cout << str;
						continue;
					}
				}
				//cout << "ItemId: " << iItemId << endl;

				//Parse Key_
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					strKey_ = strTemp;
				}
				//cout << "Key_: " << strKey_ << endl;

				//Parse Value Type
				strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					try
					{									
						iValueType = atoi(strTemp.c_str());
					}
					catch(char *str)
					{
						cout << str;
						continue;
					}
				}
				//cout << "Value type: " << iValueType << endl;

				//Parse Value
				
				if(strKey_.compare(SYSTEM_INFO) == 0 || strKey_.compare(VB_SYSTEM_INFO) == 0)
				{
					
					string strInterfaceBlock;
					int iValuePos;
					iValuePos = iPosition;
					GetBlock((const char*)pBuffer, (int&)iValuePos, (int)iLength);
					strInterfaceBlock = GetBlock((const char*)pBuffer, (int&)iValuePos, (int)iLength);
					strInterfaceBlock = strInterfaceBlock.substr(1,strInterfaceBlock.length() - 2);
					vector< InterfaceInfo > vIfInfo(GetInterfaceInfo(strInterfaceBlock.c_str()));
					for(int i = 0; i < vIfInfo.size(); i++)
					{
						cout << "strJson : " vIfInfo[i].strJson << endl;
					}
					strTemp = GetValueBlock((const char*)pBuffer, (int&)iPosition, (int)iLength);
				}
				else
					strTemp = GetToken((const char*)pBuffer, (int&)iPosition, (int)iLength);
				if(strTemp.compare("") != 0)
				{
					strValue = strTemp;
				}
				cout << "Value: " << strValue << endl;
		  }
	}

	//cout << "Datetime: " << strDatetimeSuffix << endl;
	//cout << "Suffix: " << strDatetimeSuffix << endl;
	//cout << "Log file: " <<<< endl;
	return eResult;
}

vector< InterfaceInfo > CHistoryLogParser::GetInterfaceInfo(const char* Buffer)
{
	int iStart,iEnd;
	string strTmp, strIP;
	vector< InterfaceInfo > vRes; // vector of InterfaceInfo
	
	vector<string> vInterface(CUtilities::SplitString(Buffer,";")); // split BlockInterface to vector
	for(int i = 0; i < vInterface.size(); i ++)
	{
		InterfaceInfo InfTmp;
		strTmp = vInterface[i];
		
		iEnd = strTmp.find("-");
		InfTmp.strJson = "{name:" + strTmp.substr(0,iEnd);
		
		iStart = strTmp.find("-") + 1;
		iEnd = strTmp.find(",") - iStart; 
		strIP = strTmp.substr(iStart,iEnd);
		InfTmp.strJson = InfTmp.strJson + ",ip:" + strIP;
		InfTmp.eType = GetIPType(strIP);
		
		iStart = strTmp.find(",") + 1;
		iEnd = strTmp.find(";") - iStart;
		InfTmp.strJson = InfTmp.strJson + ",mac:" + strTmp.substr(iStart,iEnd) + "}";
		
		vRes.push_back(InfTmp);
	}
	return vRes;
}


IP_TYPE CHistoryLogParser::GetIPType(const char* strIP)
{
	int iStartPrivateIPClassA, iEndPrivateIPClassA, iStartPrivateIPClassB, 
	iEndPrivateIPClassB, iStartPrivateIPClassC, iEndPrivateIPClassC, iInputIP;
	
	iStartPrivateIPClassA		= inet_addr("10.0.0.0");
	iEndPrivateIPClassA			= inet_addr("10.255.255.255");
	
	iStartPrivateIPClassB		= inet_addr("172.16.0.0");
	iEndPrivateIPClassB			= inet_addr("172.31.255.255");

	iStartPrivateIPClassC		= inet_addr("192.168.0.0");
	iEndPrivateIPClassC			= inet_addr("192.168.255.255");
	
	iInputIP					= inet_addr(strIP);
	
	if(iInputIP >= iStartPrivateIPClassA && iInputIP <= iEndPrivateIPClassA)
		return IP_PRIVATE
	if(iInputIP >= iStartPrivateIPClassB && iInputIP <= iEndPrivateIPClassB)
		return IP_PRIVATE
	if(iInputIP >= iStartPrivateIPClassC && iInputIP <= iEndPrivateIPClassC)
		return IP_PRIVATE
	return IP_PUBLIC
}


MA_RESULT CHistoryLogParser::ThreadExecute()
{
	cout << "Execute: " << endl;
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	int i = 0;
	while(true)
	{
		i+=1;
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}