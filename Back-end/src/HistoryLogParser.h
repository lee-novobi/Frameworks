#pragma once
#include "../Processor/BaseThread.h"
#include "LogParser.h"

class CHistoryConfig;
class CConfigFileParse;
class CHostController;
struct ConnectInfo;

class CHistoryLogParser: public LogParser
{
public:
	CHistoryLogParser(void);
	CHistoryLogParser(string strCfgFile);
	~CHistoryLogParser(void);
	MA_RESULT ProcessParse();

protected:
	//============Function=============//
	MA_RESULT ParseLog();
	MA_RESULT ThreadExecute();
	void Init();
	void Destroy();
	
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	char* PreParsing(int &iLen, int &iPos);
	vector< InterfaceInfo > GetInterfaceInfo(const char* Buffer);
	IP_TYPE GetIPType(const char* strIP);
	//=========================//
	CHostController *m_pActiveHostController;
	CHistoryConfig *m_pConfigObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsHostPart;
};

