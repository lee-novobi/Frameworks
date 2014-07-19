#pragma once
#include "HistoryLogParser.h"

class CWebInfoController;
class CWebInfoModel;
class CWebInfoHistoryController;
class CWebInfoHistoryModel;

class CWebInfoLogParser: public CHistoryLogParser
{
public:
	CWebInfoLogParser(string strCfgFile);
	CWebInfoLogParser();
	~CWebInfoLogParser();
protected:
	void ParseWebInfo(const HostInfo& tagHostInfo, string &strSuffix);
	bool ControllerConnect();
	
	CWebInfoController *m_pWebInfoController;
	CWebInfoModel *m_pWebInfoModel;
	CWebInfoHistoryController *m_pWebInfoHistoryController;
	CWebInfoHistoryModel *m_pWebInfoHistoryModel;
};