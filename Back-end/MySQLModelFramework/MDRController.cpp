#include "MDRController.h"
#include "../Config/LogParserConfig.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"
#include "../Model/JsonModel.h"

CMDRController::CMDRController(void)
{
}

CMDRController::CMDRController(ConnectInfo CInfo):CMySQLController(CInfo)
{
}

CMDRController::~CMDRController(void)
{
}

bool CMDRController::SelectChangedHost()
{
	try 
	{
		string strQuery = "SELECT * FROM network_tracking_changes WHERE is_sent = 0 OR is_response_success = 0;";
		SetQuery(strQuery);
		SelectQuery();
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CMDRController::GetAllMDRHost()
{
	try
	{
		while(NextRow()) 
		{
			string strServerKey  = FetchString("server_key");
			string strServerName  = FetchString("server_name");
			string strPrivateInterfaces = FetchString("private_interfaces");
			string strPublicInterfaces = FetchString("public_interfaces");
			
			BuildAPIJsonData(strServerKey, strServerName, strPrivateInterfaces, strPublicInterfaces);	
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

string CMDRController::BuildAPIJsonData(string strServerKey, string strServerName, string strPrivateInterfaces, string strPublicInterfaces)
{
	try
	{
		CJsonModel *oJsonModel = new CJsonModel();
		oJsonModel->AppendValue("Serial_Number", strServerKey);
		oJsonModel->AppendValue("Server_Name", strServerName);
		oJsonModel->AppendValue("IP_Public", strPublicInterfaces);
		oJsonModel->AppendValue("IP_Private", strPrivateInterfaces);
		cout << oJsonModel->toString() << endl;
		return oJsonModel->toString();
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}


