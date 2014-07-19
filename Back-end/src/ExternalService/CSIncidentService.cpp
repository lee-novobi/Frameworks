#include "CSIncidentService.h"

//#include "../Controller/CSAlertController.h"
//#include "../Controller/ImpactLevelController.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"
#include "../Model/JsonModel.h"
#include "soapH.h"
#include "BasicHttpBinding_USCOREISDKSuportSerivces.nsmap"

CCSIncidentService::CCSIncidentService(void)
{
}
CCSIncidentService::CCSIncidentService(string strCfgFile)
{
	//m_pActiveCSAlertController = new CCSAlertController();
	m_pActiveImpactLevelController = new CImpactLevelController()
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	m_pJsonData	  = new CJsonModel();
	Init();
}
CCSIncidentService::~CCSIncidentService(void)
{
	delete m_pJsonData;
	//delete m_pActiveCSAlertController;
	delete m_pActiveImpactLevelController;
	delete m_pActiveMapProductController;
	Destroy();
}
bool CCSIncidentService::ControllerConnect()
{
	//====================================MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	
	//if(!m_pActiveCSAlertController->Connect(CInfo))
	//	return false;
	/*if(!m_pActiveImpactLevelController->Connect(CInfo))
		return false;*/
		
	return true;
}
string CCSIncidentService::GetListIncident()
{
	try{
		string strIncidentResult = "";

		struct soap soap;
		struct _ns1__GetListINC tagGetListINCrequest;
		struct _ns1__GetListINCResponse tagGetListINCResponse;

		tagGetListINCrequest.sigkey = (char*)"sdk123";
	  
		soap_init(&soap);
		if(soap_call___ns1__GetListINC(&soap, 
								NULL  /*endpoint address*/, 
								NULL  /*soapAction*/, 
								&tagGetListINCrequest, 
								&tagGetListINCResponse
							   )== SOAP_OK)
		{
			strIncidentResult = tagGetListINCResponse.GetListINCResult;
		}
		else
		{          
			soap_print_fault(&soap, stderr); 
			CUtilities::WriteErrorLog(stderr);
		}             
	        
		soap_destroy(&soap); 
		soap_end(&soap); 
		soap_done(&soap); 
	}catch(exception &ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return strIncidentResult
}
//void CCSIncidentService::OpenListIncident(string strJsonData)
//{
//	try{
//		if(m_pJsonData->AppendArray(strJsonData))
//		{
//			ResponseINCByCS resINC;
//		}
//	}catch(exception &ex)
//	{
//		stringstream strErrorMess;
//		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
//		CUtilities::WriteErrorLog(strErrorMess.str());
//	}
//
//}