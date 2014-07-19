#include "CheckCCUAction.h"
#include "../../mysqlConnector.h"
#include "../../../Config/DBParameters.h"
#include <iostream>
using namespace std;

CCheckCCUAction::CCheckCCUAction()
{
}

CCheckCCUAction::~CCheckCCUAction()
{
}

void CCheckCCUAction::DoAct()
{	
	CmysqlConnector *conn = new CmysqlConnector(paraSqlHost, paraSqlUser, paraSqlPassword, paraSqlDBName);
	
	if(!conn->Query("SELECT * FROM hieutt"))
		conn->GetResult();
	delete conn;
}