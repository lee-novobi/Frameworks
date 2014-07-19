#pragma once
#include "../Common/Common.h"
#include "mongo/client/dbclient.h"
#include "mongo/bson/bsonobjbuilder.h"
using namespace mongo;
using namespace std;

class CMongodbModel
{
public:
	CMongodbModel(void);
	~CMongodbModel(void);
	
	// void SetRecordBson(string strFieldName, string strVal);
	// void SetRecordBson(string strFieldName, int iVal);
	// void SetRecordBson(string strFieldName, long long lVal);
	// void SetRecordBson(string strFieldName);
	
	BSONObj GetRecordBson();
	Query GetObjectIdQuery();
	
	virtual void PrepareRecord() = 0;
	virtual void DestroyData() = 0;
	inline void ReleaseBuilder()
	{
		if (m_pRecordBuilder)
		{
			delete m_pRecordBuilder;
			m_pRecordBuilder = new BSONObjBuilder();
			m_strObjId = "";
		}
	}
	
//=================================Set Get Propertise ==============================
	inline void SetObjId(string strObjId)
	{
		m_strObjId = strObjId;
	}
	
protected:
	Query m_pConditionQuery;
	BSONObjBuilder *m_pRecordBuilder;
	string m_strObjId;
	
};
