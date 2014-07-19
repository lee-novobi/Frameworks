//#include "StdAfx.h"
#include "DCAlertController.h"
#include "../ExternalService/CurlService.h"
#include "../Common/DBCommon.h"

CDCAlertController::CDCAlertController(void)
{
	m_strTableName = ".dc_alerts";
}

CDCAlertController::~CDCAlertController(void)
{
}

bool CDCAlertController::ResetOperation(BSONObj bsonCondition)
{
	if(m_bIsConnected)
	{
		try{
			m_connDB.update(m_strTableName, QUERY(RECORD_ID<<OID(CUtilities::RemoveBraces(bsonCondition[SOURCE_ID].toString(false)))), 
			BSON("$set"<<BSON(IS_SYNC<<0)),false,true);
			return true;
		}
		catch(exception& ex)
		{
			string err = m_connDB.getLastError();
			stringstream strErrorMess;
			strErrorMess << ex.what() << ": " << err << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;	
}