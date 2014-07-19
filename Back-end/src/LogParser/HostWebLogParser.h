#pragma once
#include "../Processor/BaseThread.h"
#include "LogParser.h"

class CLogParserData;
class CConfigFileParse;
class CHostWebController;
class CHostWebModel;
struct ConnectInfo;

class CHostWebLogParser: public LogParser
{
public:
	CHostWebLogParser(void);
	CHostWebLogParser(string strCfgFile);
	~CHostWebLogParser(void);
	MA_RESULT ProcessParse();

protected:
	//============Function=============//
	MA_RESULT ParseLog();
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	char* PreParsing(int &iLen, int &iPos);
	//=========================//
	CHostWebController *m_pHostWebController;
	CHostWebModel *m_pHostWebModel;
	CLogParserData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsHostWebPart;
};
