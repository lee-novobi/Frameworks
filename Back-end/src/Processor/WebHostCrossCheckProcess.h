#pragma once
#include "../Common/Common.h"

class CZbxWebHostController;
class CHostWebController;
class CZbxWebHostModel;
class CHostWebModel;
class CConfigFileParse;
struct ConnectInfo;

class CWebHostCrossCheckProcess
{
public:
	CWebHostCrossCheckProcess(void);
	CWebHostCrossCheckProcess(string strCfgFile);
	~CWebHostCrossCheckProcess(void);
	MA_RESULT CrossCheckProcess();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	virtual int CreateModel()
	{
	}
	//////////////////////////////////////////////

	CZbxWebHostController *m_pZbxWebHostController;
	CHostWebController *m_pHostWebController;
	
	CZbxWebHostModel *m_pZbxWebHostModel;
	CHostWebModel *m_pHostWebModel;
	
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};