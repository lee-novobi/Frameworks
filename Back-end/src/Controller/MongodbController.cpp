#include "MongodbController.h"
#include "../Common/DBCommon.h"
ConnectInfo g_CMongoInfo;

CMongodbController::CMongodbController(void)
{
	m_bIsConnected = false;
	m_strTableName = "";
	new(&m_connDB) DBClientConnection(true);
}

CMongodbController::~CMongodbController(void)
{
	DestroyData();
}

bool CMongodbController::Connect(ConnectInfo CInfo)
{
	string strLog;
	string strErrMsg;
	
	if (m_bIsConnected)
	{
		return true;
	}
	try{
		if(!m_connDB.connect(CInfo.strHost, strErrMsg)){
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Connect","connect->FAIL:" + CInfo.strHost + "|" + strErrMsg);
			CUtilities::WriteErrorLog(strLog);
			return false;
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Connect","Connect.exception:" + CInfo.strHost + "|" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	
	try{
		strErrMsg.clear();
		if (!m_connDB.auth(CInfo.strSource, CInfo.strUser, CInfo.strPass, strErrMsg))
		{
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Connect","auth->FAIL:" + CInfo.strSource + "|" + CInfo.strUser + "|" + CInfo.strPass + "|" + strErrMsg);
			CUtilities::WriteErrorLog(strLog);
			return false;
		}
		m_bIsConnected = true;
		
		g_CMongoInfo = CInfo;
		m_strTableName = CInfo.strSource + m_strTableName; 
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Connect","Authen.exception:" + CInfo.strSource + "|" + CInfo.strUser + "|" + CInfo.strPass + "|" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}

	return false;
}

bool CMongodbController::Reconnect()
{
	string strLog;
	string strErrMsg;
	try{
		if(!m_connDB.connect(g_CMongoInfo.strHost, strErrMsg)){
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Reconnect","connect->FAIL:" + g_CMongoInfo.strHost + "|" + strErrMsg);
			CUtilities::WriteErrorLog(strLog);
			return false;
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Reconnect","Connect.exception:" + g_CMongoInfo.strHost + "|" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	try{
		strErrMsg.clear();
		if (!m_connDB.auth(g_CMongoInfo.strSource, g_CMongoInfo.strUser, g_CMongoInfo.strPass, strErrMsg))
		{
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Reconnect","auth->FAIL:" + g_CMongoInfo.strSource + "|" + g_CMongoInfo.strUser + "|" + g_CMongoInfo.strPass + "|" + strErrMsg);
			CUtilities::WriteErrorLog(strLog);
			return false;
		}
		m_bIsConnected = true;
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Reconnect","Authen.exception:" + g_CMongoInfo.strSource + "|" + g_CMongoInfo.strUser + "|" + g_CMongoInfo.strPass + "|" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}


bool CMongodbController::FindDB(Query oCondition)
{	
	string strLog;
	if(m_bIsConnected)
	{
		try{
			m_ptrResultCursor = m_connDB.query(m_strTableName, oCondition.sort(CLOCK));
			if(m_ptrResultCursor->more())
				return true;

			return false;
		}
		catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "FindDB","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
		}
	}
	return false;
}

bool CMongodbController::FindDB(string strTable, Query oCondition)
{	
	m_strTableName = g_CMongoInfo.strSource + "." + strTable;
	return FindDB(oCondition);
}

bool CMongodbController::InsertDB(string strTable, BSONObj oCondition, BSONObj oRecord)
{
	m_strTableName = g_CMongoInfo.strSource + "." + strTable;
	return InsertDB(oCondition, oRecord);
}

bool CMongodbController::InsertDB(BSONObj oCondition, BSONObj oRecord)
{
	string strLog;
	if(m_bIsConnected)
	{
		if(!IsRecordExisted(Query(oCondition)) || oCondition.isEmpty())
		{
			try{
				m_connDB.insert(m_strTableName, oRecord);
				return true;
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
				strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "InsertDB","exception:" + strErrorMess.str());
				CUtilities::WriteErrorLog(strLog);
			}
		}
		else
		{
			return UpdateDB(Query(oCondition), oRecord);
		}
	}
	return false;
}

bool CMongodbController::InsertDBPartition(BSONObj bsonCondition, BSONObj bsonRecord, BSONObj bsonKeysIndex, string strSuffix)
{
	string strLog;
	if(m_bIsConnected)
	{
		if(!IsRecordExisted(Query(bsonCondition)))
		{
			try{
				m_connDB.insert(m_strTableName + "_" + strSuffix, bsonRecord);
				if(!bsonKeysIndex.isEmpty())
				{
					m_connDB.ensureIndex(m_strTableName + "_" + strSuffix, bsonKeysIndex);
				}
				return true;
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
				strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "InsertDBPartition","exception:" + strErrorMess.str());
				CUtilities::WriteErrorLog(strLog);
			}
		}
		else
		{
			return UpdateDB(Query(bsonCondition), bsonRecord);
		}
	}
	return false;
}

bool CMongodbController::UpdateDB(Query queryCondition, BSONObj bsonRecord)
{
	string strLog;
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, queryCondition, BSON("$set"<<bsonRecord), false, true);
			return true;
		}
		catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "UpdateDB","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
		}
	}
	return false;
	
}

bool CMongodbController::UpdateDB(string strTable, Query queryCondition, BSONObj bsonRecord)
{
	//string strLog;
	m_strTableName = g_CMongoInfo.strSource + "." + strTable;
	return 	UpdateDB(queryCondition, bsonRecord);
}

bool CMongodbController::RemoveDB(Query queryCondition)
{
	string strLog;
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.remove(m_strTableName, queryCondition);
			return true;
		}
		catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "RemoveDB","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
		}
	}
	return false;
	
}

bool CMongodbController::RemoveDB(string strTable, Query queryCondition)
{
	m_strTableName = g_CMongoInfo.strSource + "." + strTable;
	return RemoveDB(queryCondition);
}

long long CMongodbController::Count(BSONObj bsonCondition)
{
	string strLog;
	long long lCount = 0;

	if (m_bIsConnected)
	{
		try{
			lCount = m_connDB.count(m_strTableName, bsonCondition);
		}
		catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "Count","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
		}
	}

	return lCount;
}

bool CMongodbController::IsRecordExisted(Query queryCondition)
{
	string strLog;
	auto_ptr<DBClientCursor> ptrTmp;
	ptrTmp = m_connDB.query(m_strTableName, queryCondition,1);
	if(ptrTmp->more())
	{
		ptrTmp.reset();
		return true;
	}
	ptrTmp.reset();
	return false;
}

bool CMongodbController::IsRecordExisted(string strTable, Query queryCondition)
{
	m_strTableName = g_CMongoInfo.strSource + "." + strTable;
	return IsRecordExisted(queryCondition);
}

bool CMongodbController::NextRecord()
{
	string strLog;
	try
	{
		if(m_ptrResultCursor->more())
		{
			m_CurrResultObj = m_ptrResultCursor->nextSafe();
			return true;
		}
		return false;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "NextRecord","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

string CMongodbController::GetStringResultVal(string strFieldName)
{
	string strLog;
	try
	{
		if(m_CurrResultObj.isEmpty())
			return "";
		return m_CurrResultObj[strFieldName].toString(false);
	}
	catch(exception& ex)
	{
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "GetStringResultVal","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

string CMongodbController::GetFieldString(string strFieldName)
{
	string strLog;
	try
	{
		if(m_CurrResultObj.isEmpty())
			return "";
		return m_CurrResultObj.getStringField((const char*)strFieldName.c_str());
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "GetStringResultVal2","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

int CMongodbController::GetIntResultVal(string strFieldName)
{
	string strLog;
	try
	{
		if(m_CurrResultObj.isEmpty())
			return 0;
		return m_CurrResultObj[strFieldName]._numberInt();
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "GetIntResultVal","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

long long CMongodbController::GetLongResultVal(string strFieldName)
{
	string strLog;
	try
	{
		if(m_CurrResultObj.isEmpty())
			return 0;
		return m_CurrResultObj[strFieldName]._numberLong();
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "GetLongResultVal","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

vector<long long> CMongodbController::GetArrayLongResultVal(string strFieldName)
{
	vector<long long> vtResult;
	
	string strLog;
	try
	{
		if(m_CurrResultObj.isEmpty())
			return vtResult;

		BSONObj bo = m_CurrResultObj[strFieldName].Obj();
        bo.Vals(vtResult);
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "GetLongResultVal","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return vtResult;
}

bool CMongodbController::UpdateSynced(string strSourceId)
{
	string strLog;
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, QUERY(RECORD_ID<<OID(strSourceId)), BSON("$set"<<BSON(IS_SYNC<<1)), false, true);
			return true;
		}
		catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(BUG_MSG, "CMongodbController", "UpdateSynced","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
		}
	}
	return false;
}