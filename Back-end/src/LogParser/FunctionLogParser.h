#pragma once
#include "LogParser.h"
#include "../Common/Common.h"

class CFunctionController;
class CFunctionModel;
class CLogParserData;
class CConfigFileParse;
struct ConnectInfo;

class CFunctionLogParser:public LogParser
{
public:
	CFunctionLogParser(void);
	CFunctionLogParser(string strCfgFile);
	~CFunctionLogParser(void);
	MA_RESULT ProcessParse();
	
protected:
	//============Function=============//
	MA_RESULT ParseLog();
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	void* PreParsing(int &iLen, int &iPos);
	//=========================//
	CFunctionController *m_pFunctionController;
	CFunctionModel *m_pFunctionModel;
	CLogParserData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsFunctionPart;
};
