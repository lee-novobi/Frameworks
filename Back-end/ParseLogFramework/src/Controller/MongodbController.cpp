#include "MongodbController.h"
#include "../Common/DBCommon.h"
ConnectInfo g_CMongoInfo;

CMongodbController::CMongodbController(void)
{
	m_bIsConnected = false;
}

CMongodbController::~CMongodbController(void)
{
	DestroyData();
}

bool CMongodbController::Connect(ConnectInfo CInfo)
{
	if (m_bIsConnected)
	{
		return true;
	}
	
	string strErrMsg;
	if(!m_connDB.connect(CInfo.strHost, strErrMsg)){
		CUtilities::WriteErrorLog(strErrMsg);
		return false;
	}
	
	strErrMsg.clear();
	if (!m_connDB.auth(CInfo.strSource, CInfo.strUser, CInfo.strPass, strErrMsg))
	{
		CUtilities::WriteErrorLog(strErrMsg);
		return false;
	}
	
	m_bIsConnected = true;
	
	g_CMongoInfo = CInfo;
	m_strTableName = CInfo.strSource + m_strTableName; 
	return true;
}

bool CMongodbController::Reconnect()
{
	string strErrMsg;
	if(!m_connDB.connect(g_CMongoInfo.strHost, strErrMsg)){
		CUtilities::WriteErrorLog(strErrMsg);
		return false;
	}
	
	strErrMsg.clear();
	if (!m_connDB.auth(g_CMongoInfo.strSource, g_CMongoInfo.strUser, g_CMongoInfo.strPass, strErrMsg))
	{
		CUtilities::WriteErrorLog(strErrMsg);
		return false;
	}
	
	m_bIsConnected = true;
	return true;
}


bool CMongodbController::FindDB(Query oCondition)
{	
	if (m_bIsConnected)
	{
		try{
			m_ptrResultCursor = m_connDB.query(m_strTableName, oCondition.sort(RECORD_ID));
			if(m_ptrResultCursor->more())
				return true;
			return false;
		}catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}

bool CMongodbController::InsertDB(BSONObj oCondition, BSONObj oRecord)
{
	if(m_bIsConnected)
	{
		if(!IsRecordExisted(Query(oCondition)))
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
			UpdateDB(Query(oCondition), oRecord);
		}
	}
	return false;
}

bool CMongodbController::InsertDBPartition(BSONObj bsonCondition, BSONObj bsonRecord, BSONObj bsonKeysIndex, string strSuffix)
{
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
			}catch(exception& ex)
			{
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime()
				<< "bsonCondition: " << bsonCondition.toString() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		else
		{
			UpdateDB(Query(bsonCondition), bsonRecord);
		}
	}
	return false;
}

bool CMongodbController::UpdateDB(Query queryCondition, BSONObj bsonRecord)
{
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
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() 
			<< "queryCondition: " << queryCondition.toString() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
	
}

long long CMongodbController::Count(BSONObj bsonCondition)
{
	long long lCount = 0;

	if (m_bIsConnected)
	{
		try{
			lCount = m_connDB.count(m_strTableName, bsonCondition);
		}catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() 
			<< "bsonCondition: " << bsonCondition.toString() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}

	return lCount;
}

bool CMongodbController::IsRecordExisted(Query queryCondition)
{
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

bool CMongodbController::NextRecord()
{
	try
	{
		if(m_ptrResultCursor->more())
		{
			m_CurrResultObj = m_ptrResultCursor->nextSafe();
			return true;
		}
		return false;
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

string CMongodbController::GetStringResultVal(string strFieldName)
{
	try
	{
		if(m_CurrResultObj.isEmpty())
			return "";
		return m_CurrResultObj[strFieldName].toString(false);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

int CMongodbController::GetIntResultVal(string strFieldName)
{
	try
	{
		if(m_CurrResultObj.isEmpty())
			return 0;
		return m_CurrResultObj[strFieldName]._numberInt();
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

long long CMongodbController::GetLongResultVal(string strFieldName)
{
	try
	{
		if(m_CurrResultObj.isEmpty())
			return 0;
		return m_CurrResultObj[strFieldName]._numberLong();
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}
