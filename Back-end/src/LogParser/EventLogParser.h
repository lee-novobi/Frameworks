#pragma once
#include "LogParser.h"
#include "../Common/Common.h"

class CEventController;
class CEventModel;
class CLogParserData;
class CTriggerController;
class CTriggerModel;
class CConfigFileParse;
struct ConnectInfo;

class CEventLogParser:public LogParser
{
public:
	CEventLogParser(void);
	CEventLogParser(string strCfgFile);
	~CEventLogParser(void);
	MA_RESULT ProcessParse();
protected:
	//============Function=============//
	MA_RESULT ParseLog();
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	void* PreParsing(int &iLen, int &iPos);
	//=========================//
	CEventController *m_pEventController;
	CEventModel *m_pEventModel;
	CTriggerController *m_pTriggerController;
	CTriggerModel *m_pTriggerModel;
	CLogParserData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsEventPart;
};
