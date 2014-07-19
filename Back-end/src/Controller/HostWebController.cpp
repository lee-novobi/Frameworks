//#include "StdAfx.h"
#include "HostWebController.h"
#include "../Common/DBCommon.h"

CHostWebController::CHostWebController(void)
{
	m_strTableName = ".host_web";
}

CHostWebController::~CHostWebController(void)
{
}

bool CHostWebController::UpdateDB(Query queryCondition, BSONObj bsonRecord)
{
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, queryCondition, BSON("$set"<<BSON( HOST_NAME<<CUtilities::RemoveBraces(bsonRecord[HOST_NAME].toString(false)) 
																				<<NAME<<CUtilities::RemoveBraces(bsonRecord[NAME].toString(false)))), false, true);
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() 
			<< "queryCondition: " << queryCondition.toString() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
	
}

bool CHostWebController::UpdateDelete(Query queryCondition, BSONObj bsonRecord)
{
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, queryCondition, BSON("$set"<<BSON( DELETE<<bsonRecord[DELETE]._numberInt() )), false, true);
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() 
			<< "queryCondition: " << queryCondition.toString() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
	
}

