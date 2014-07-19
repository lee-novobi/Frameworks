//#include "StdAfx.h"
#include "AlertSyncController.h"
#include "../Common/DBCommon.h"

CAlertSyncController::CAlertSyncController(void)
{
	m_strTableName = ".monitoring_assistant_alerts";
}

CAlertSyncController::~CAlertSyncController(void)
{
}

bool CAlertSyncController::InsertDB(BSONObj oCondition, BSONObj oRecord)
{
	if(m_bIsConnected)
	{
		if(!IsRecordExisted(Query(oCondition)) || oCondition.isEmpty())
		{
			try{
				m_connDB.insert(m_strTableName, oRecord);
				return true;
			}catch(exception& ex)
			{
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		else
		{
			return UpdateDB(Query(oCondition), oRecord);
		}
	}
	return false;
}


bool CAlertSyncController::UpdateDB(Query queryCondition, BSONObj bsonRecord)
{
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, queryCondition, 
			BSON("$set"<<BSON(ZBX_EVENT_ID<<bsonRecord[ZBX_EVENT_ID]._numberLong()
						<<IS_SHOW<<bsonRecord[IS_SHOW]._numberInt()
						<<CLOCK<<bsonRecord[CLOCK]._numberLong())), false, true);
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


bool CAlertSyncController::UpdateMaintenance(Query queryCondition, BSONObj bsonRecord)
{
	if(m_bIsConnected)
	{
		if(IsRecordExisted(queryCondition))
		{
			try
			{
				m_connDB.update(m_strTableName, queryCondition, 
				BSON("$set"<<BSON(ZBX_MAINTENANCE<<bsonRecord[ZBX_MAINTENANCE]._numberInt())), false, true);
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
	}
	return false;
}

bool CAlertSyncController::HideAlertNotInSrcId(string strSrcFrom, vector<string> vStrAlertId)
{
	BSONArrayBuilder babAlertId;
	if(m_bIsConnected)
	{
			try
			{
				if(vStrAlertId.empty())
				{
					m_connDB.update(m_strTableName, QUERY(SOURCE_FROM<<strSrcFrom<<IS_SHOW<<1),BSON("$set"<<BSON(IS_SHOW<<0)),false,true);
				}
				else
				{
					for(int i = 0; i < vStrAlertId.size(); i++){
						babAlertId << vStrAlertId[i];
					}
					m_connDB.update(m_strTableName, QUERY(SOURCE_ID<<BSON("$nin"<<babAlertId.arr())<<SOURCE_FROM<<strSrcFrom<<IS_SHOW<<1),BSON("$set"<<BSON(IS_SHOW<<0)),false,true);
				}
				return true;
			}
			catch(exception& ex)
			{
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
	}
	return false;
}

bool CAlertSyncController::RemoveAlertNotInTicketId(string strSrcFrom, vector<string> vTicketId)
{
	BSONArrayBuilder babTicketId;
	if(m_bIsConnected)
	{
		try
		{
			if(vTicketId.empty())
			{
				m_connDB.remove(m_strTableName, QUERY(SOURCE_FROM<<strSrcFrom));
			}
			else
			{
				for(int i = 0; i < vTicketId.size(); i++)
					babTicketId << vTicketId[i];
				m_connDB.remove(m_strTableName, QUERY(SOURCE_FROM<<strSrcFrom<<TICKET_ID<<BSON("$nin"<<babTicketId.arr())));
			}
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}

bool CAlertSyncController::ChangeIsShowState(BSONObj oCondition)
{
	if(m_bIsConnected)
	{
		// cout << oCondition[SOURCE_FROM].toString(false) << endl;
		// cout << oCondition[SOURCE_ID].toString(false) << endl;
		Query queryCondition = Query(oCondition);
		try
		{
			m_connDB.update(m_strTableName, queryCondition, 
				BSON("$set"<<BSON(IS_SHOW<<2)), false, true);
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}

bool CAlertSyncController::ChangeZbxIsShowState(BSONObj oCondition)
{
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, oCondition, 
				BSON("$set"<<BSON(IS_SHOW<<0)), false, true);
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}

bool CAlertSyncController::HideAlertByVServerId(vector<long long> v_lServerId)
{
	BSONArrayBuilder babServerId;
	if(m_bIsConnected)
	{
		try
		{
			if(!v_lServerId.empty())
			{
				for(int i = 0; i < v_lServerId.size(); i++)
					babServerId << v_lServerId[i];
				m_connDB.update(m_strTableName, QUERY(ZBX_SERVER_ID<<BSON("$in"<<babServerId.arr())<<IS_SHOW<<1), BSON("$set"<<BSON(IS_SHOW<<0)),false,true);
			}
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}

bool CAlertSyncController::HideAlertByVTriggerId(vector<long long> v_lTriggerId)
{
	BSONArrayBuilder babTriggerId;
	if(m_bIsConnected)
	{
		try
		{
			if(!v_lTriggerId.empty())
			{
				for(int i = 0; i < v_lTriggerId.size(); i++)
					babTriggerId << v_lTriggerId[i];
				m_connDB.update(m_strTableName, QUERY(ZBX_TRIGGER_ID<<BSON("$in"<<babTriggerId.arr())<<IS_SHOW<<1), BSON("$set"<<BSON(IS_SHOW<<0)),false,true);
			}
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}