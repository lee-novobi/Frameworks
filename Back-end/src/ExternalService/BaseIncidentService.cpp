#include "BaseIncidentService.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CBaseIncidentService::CBaseIncidentService(void)
{
}
CBaseIncidentService::~CBaseIncidentService(void)
{
}
CBaseIncidentService::Init()
{
	ControllerConnect();
}
void CBaseIncidentService::Destroy()
{
	delete m_pConfigFile;
}
ConnectInfo CBaseIncidentService::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetData(DBType,HOST);
	CInfo.strUser = m_pConfigFile->GetData(DBType,USER);
	CInfo.strPass = m_pConfigFile->GetData(DBType,PASS);
	CInfo.strSource = m_pConfigFile->GetData(DBType,SRC);
	
	if(m_pConfigFile->GetData(DBType,PORT).compare("") != 0)
		CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetData(DBType,PORT);

	return CInfo;
}

