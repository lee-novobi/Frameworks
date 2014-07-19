#include "MongodbModel.h"
#include "../Common/DBCommon.h"
CMongodbModel::CMongodbModel(void)
{	
	m_pRecordBuilder = new BSONObjBuilder();
}

CMongodbModel::~CMongodbModel(void)
{
	m_pRecordBuilder->obj();
	delete m_pRecordBuilder;
}


BSONObj CMongodbModel::GetRecordBson()
{
	return m_pRecordBuilder->asTempObj();
}

Query CMongodbModel::GetObjectIdQuery()
{
	Query queryQueryResult = QUERY(RECORD_ID<<OID(m_strObjId));
	return queryQueryResult;
}