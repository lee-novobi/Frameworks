#pragma once
#include "../Processor/BaseThread.h"
#include "LogParser.h"

class CLogParserData;
class CConfigFileParse;
class CItemController;
class CItemModel;
struct ConnectInfo;

class CItemLogParser: public LogParser
{
public:
	CItemLogParser(void);
	CItemLogParser(string strCfgFile);
	~CItemLogParser(void);
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
	CItemController *m_pItemController;
	CItemModel *m_pItemModel;
	CLogParserData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsItemPart;
};
