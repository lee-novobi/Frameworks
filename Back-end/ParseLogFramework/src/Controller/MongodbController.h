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
	bool InsertDB(BSONObj bsonCondition, BSONObj bsonRecord);
	bool InsertDBPartition(BSONObj bsonCondition, BSONObj bsonRecord, BSONObj bsonKeysIndex, string strSuffix);
	bool UpdateDB(Query queryCondition, BSONObj bsonRecord);
	long long Count(BSONObj bsonCondition = BSONObj());
	bool IsRecordExisted(Query queryCondition = Query());
	bool NextRecord();
	
	string GetStringResultVal(string strFieldName);
	int GetIntResultVal(string strFieldName);
	long long GetLongResultVal(string strFieldName);
	
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
};
