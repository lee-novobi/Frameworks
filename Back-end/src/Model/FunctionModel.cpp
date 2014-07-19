#include "FunctionModel.h"
#include "../Common/DBCommon.h"
CFunctionModel::CFunctionModel(void)
{
}

CFunctionModel::~CFunctionModel(void)
{
}

BSONObj CFunctionModel::GetUniqueFunctionBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<m_iZbxServerId<<FUNCTION_ID<<m_lFunctionId);
	return bsonQueryResult;
}

Query CFunctionModel::GetFunctionByTriggerIdBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	Query queryQueryResult = QUERY(ZBX_SERVER_ID<<m_iZbxServerId<<TRIGGER_ID<<m_lTriggerId);
	return queryQueryResult;
}

void CFunctionModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(FUNCTION_ID, m_lFunctionId);
		m_pRecordBuilder->append(ZBX_SERVER_ID, m_iZbxServerId);
		m_pRecordBuilder->append(FUNCTION_NAME, m_strFunction);
		m_pRecordBuilder->append(PARAMETER, m_strParameter);
		m_pRecordBuilder->append(TRIGGER_ID, m_lTriggerId);
		m_pRecordBuilder->append(ITEM_ID, m_lItemId);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CFunctionModel::DestroyData()
{
	ReleaseBuilder();
	m_iZbxServerId = 0;
	m_lFunctionId = m_lTriggerId = m_lItemId = 0;
	m_strFunction = m_strParameter = "";
}