#include "CSServiceController.h"
#include "../CSService/CSServiceReference.h"
#include "../Utilities/Utilities.h"

CCSIncident::CCSIncident(void)
{
}
CCSIncident::~CCSIncident(void)
{
}
string CCSIncident::GetCurrTime(const char* pFormat)
{
	string strCurTime;
	time_t rawtime;
	struct tm * timeinfo;
	char buffer [80];

	time (&rawtime);
	timeinfo = localtime (&rawtime);
	strftime (buffer,80,pFormat,timeinfo);
	strCurTime = buffer;
	return strCurTime;
}
string CCSIncident::UpdateStatusINC()
{
	string strResponse;
	string strCloseDate;
	short INCStatusID = 27;
	strCloseDate = GetCurrTime("%Y-%m-%d %H:%M:%S");

	m_pINCCode = (char*)"INC-00091";
	m_pINCStatusID = &INCStatusID;	
	m_pITSMCode = (char*)"";	
	m_pCreatedBy = (char*)"sdk";	
	m_pComment = (char*)"";	
	m_pITSMCloseDate = "";//(char*)strCloseDate.c_str();
	cout << "m_pINCCode:" << m_pINCCode << endl;
	strResponse = CallUpdateStatusCSINC(m_pINCCode, m_pINCStatusID, m_pITSMCode, m_pCreatedBy, m_pComment, m_pITSMCloseDate);
	if(strResponse == CODE_ERROR_INIT || strResponse == CODE_ERROR_UPDATE_STATUS_INC)
	{
		// string strErrorMsg;
		// strErrorMsg = CUtilities::FormatLog("Error", "CSServiceController", "UpdateStatusINC", MSG_ERROR_INIT);
		// CUtilities::WriteErrorLog(strErrorMsg);
		cout << MSG_ERROR_INIT << endl;
	}
	
	return strResponse;
}