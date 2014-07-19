#include "SO6AlertController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CSO6AlertController::CSO6AlertController (void)
{
	m_strTableName = ".alert";
}

CSO6AlertController::CSO6AlertController (string strDBName)
{
	m_strTableName = strDBName + ".alert";
}

bool CSO6AlertController::SelectCritical()
{
	try{
		string strQuery = "SELECT * FROM `alert_history` WHERE `priority` = 'Critical';";
		SelectQuery(strQuery.c_str());
		if(m_pResult->row_count==0)
		{
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "SO6AlertController", "SelectCritical",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

CSO6AlertController::~CSO6AlertController (void)
{
}
