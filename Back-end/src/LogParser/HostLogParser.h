#pragma once
#include "LogParser.h"

class CLogParserConfig;
class CConfigFileParse;
class CHostController;
struct ConnectInfo;

class CHostLogParser: public LogParser
{
public:
	CHostLogParser(void);
	CHostLogParser(string strCfgFile);
	~CHostLogParser(void);
	
	MA_RESULT ProcessParse();
protected:
	//============Function=============//
	MA_RESULT ParseLog();
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	char* PreParsing(int &iLen, int &iPos);
	bool CheckStop();
	//=========================//
	CHostController *m_pActiveHostController;
	CLogParserConfig *m_pConfigObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsHostPart;
};

