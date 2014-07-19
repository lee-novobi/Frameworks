#pragma once
#include "LogParser.h"

class CHistoryData;
class CConfigFileParse;
class CMongodbController;
class CMongodbModel;
struct ConnectInfo;

struct HostInfo
{
	long long lServerId;
	int iZbxServerId;
	long lHostId;
	long lItemId;
	string strSerialNumber;
	string strHost;
	string strHostName;
	string strZbIpAddress;
	int iMaintainance;
	long long lClock;
	string strKey_;
	string strValue;
};

class CHistoryLogParser: public LogParser
{
public:
	CHistoryLogParser(void);
	CHistoryLogParser(string strCfgFile);
	~CHistoryLogParser(void);
	MA_RESULT ProcessParse();

protected:
	MA_RESULT ParseLog();
	IP_TYPE GetIPType(string strIP);
	ConnectInfo GetConnectInfo(string DBType);
	void Init();
	void Destroy();
	void ResetHostInfo(HostInfo& tagHostInfo);
	bool CheckStop();
	
	virtual bool ControllerConnect() = 0;
	virtual void ParseSystemInfo(const char* Buffer, int& iPosition, int iLength, long long lServerId, map<long long,string> &mapHostMacDict, const HostInfo& tagHostInfo)
	{
		GetValueBlock(Buffer, iPosition, iLength);
	}
	virtual void ParseWebInfo(const HostInfo& tagHostInfo, string &strSuffix)
	{
		//GetValueBlock(pBuffer, &iPosition, (iLength);
	}
	
	CHistoryData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
};

