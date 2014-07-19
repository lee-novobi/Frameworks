#pragma once
#include "../Common/Common.h"

class CConfigFileParse;
struct ConnectInfo;

class CBaseIncidentService
{
public:
	CBaseIncidentService(void);
	~CBaseIncidentService(void);
		  
protected:
	//-------------- Function -----------------//
	void Init();
	void Destroy();
	virtual bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	
	//-------------- Attributes ---------------//
	CConfigFileParse *m_pConfigFile;
};