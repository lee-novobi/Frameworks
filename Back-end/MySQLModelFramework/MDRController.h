#pragma once
#include "MySQLController.h"

class CLogParserConfig;
class CConfigFileParse;

struct ConnectInfo;

class CMDRController: public CMySQLController
{
public:
	CMDRController(void);
	CMDRController(ConnectInfo CInfo);
	~CMDRController(void);
	
	void GetAllMDRHost();

protected:
	CLogParserConfig *m_pConfigObj;
	CConfigFileParse *m_pConfigFile;
	
	string BuildAPIJsonData(string, string, string, string);

};

