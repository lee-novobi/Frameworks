#pragma once
#include "LogParser.h"
#include "../Common/Common.h"

class CTriggerController;
class CTriggerModel;
class CLogParserData;
class CConfigFileParse;
struct ConnectInfo;

class CTriggerLogParser:public LogParser
{
public:
	CTriggerLogParser(void);
	CTriggerLogParser(string strCfgFile);
	~CTriggerLogParser(void);
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
	CTriggerController *m_pTriggerController;
	CTriggerModel *m_pTriggerModel;
	CLogParserData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsTriggerPart;
};
