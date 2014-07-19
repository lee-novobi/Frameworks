#pragma once
#include "../Common/Common.h"
#include "mongo/client/dbclient.h"
using namespace mongo;

struct ConnectInfo;

#define MAX_STRING_VALUE_LEN	1024
typedef struct _tMongodbField
{
	string strName;
	int iDataType;
	union
	{
		int iValue;
		float fValue;
		wchar_t strValue[MAX_STRING_VALUE_LEN];
	};
} tMongodbField;

typedef std::vector<tMongodbField> MongodbFieldArray;

class CMongodbController
{
public:
	CMongodbController(void);
	~CMongodbController(void);
	
	bool Connect(ConnectInfo CInfo);
	bool Reconnect();
	bool FindDB(Query queryCondition = Query());
	bool FindDB(string strTable, Query queryCondition = Query());
	bool InsertDB(string strTable, BSONObj bsonCondition, BSONObj bsonRecord);
	bool InsertDB(BSONObj bsonCondition, BSONObj bsonRecord);
	bool InsertDBPartition(BSONObj bsonCondition, BSONObj bsonRecord, BSONObj bsonKeysIndex, string strSuffix);
	bool UpdateDB(Query queryCondition, BSONObj bsonRecord);
	bool UpdateDB(string strTable, Query queryCondition, BSONObj bsonRecord);
	bool RemoveDB(Query queryCondition);
	bool RemoveDB(string strTable,Query queryCondition);
	long long Count(BSONObj bsonCondition = BSONObj());
	bool IsRecordExisted(Query queryCondition = Query());
	bool IsRecordExisted(string strTable, Query queryCondition = Query());
	bool NextRecord();
	bool UpdateSynced(string strSourceId);
	
	string GetJsonStringResultVal(string strFieldName);
	string GetStringResultVal(string strFieldName);
	string GetFieldString(string strFieldName);
	int GetIntResultVal(string strFieldName);
	long long GetLongResultVal(string strFieldName);
	vector<long long> GetArrayLongResultVal(string strFieldName);
	
	inline void DestroyData()
	{
		m_ptrResultCursor.reset();
		m_CurrResultObj = BSONObj();
	}
	
protected:
	DBClientConnection m_connDB;
	bool m_bIsConnected;
	string m_strTableName;
	auto_ptr<DBClientCursor> m_ptrResultCursor;
	BSONObj m_CurrResultObj;
	BSONObjBuilder *m_pModel;
};
