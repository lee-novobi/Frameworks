#include "MongodbModel.h"
#include "../Common/DBCommon.h"
CMongodbModel::CMongodbModel(void)
{	
	m_pRecordBuilder = new BSONObjBuilder();
}

CMongodbModel::~CMongodbModel(void)
{
	ReleaseBuilder();
}


BSONObj CMongodbModel::GetRecordBson()
{
	return m_pRecordBuilder->asTempObj();
}

Query CMongodbModel::GetObjectIdQuery()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	Query queryQueryResult = QUERY(RECORD_ID<<OID(m_strObjId));
	return queryQueryResult;
}