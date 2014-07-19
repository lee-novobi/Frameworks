#include "ItemModel.h"
#include "../Common/DBCommon.h"
CItemModel::CItemModel(void)
{
}

CItemModel::~CItemModel(void)
{
}

BSONObj CItemModel::GetUniqueItemBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<Obj[ZBX_SERVER_ID]._numberInt()<<ITEM_ID<<Obj[ITEM_ID]._numberLong());
	return bsonQueryResult;
}


void CItemModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(CLOCK, m_lClock);
		m_pRecordBuilder->append(ZBX_SERVER_ID, m_iZbxServerId);
		m_pRecordBuilder->append(DESCRIPTION, m_strDescription);
		m_pRecordBuilder->append(STATUS, m_iStatus);
		m_pRecordBuilder->append(TRIGGER_ID, m_lTriggerId);
		m_pRecordBuilder->append(HOST_ID, m_lHostId);
		m_pRecordBuilder->append(SERVER_ID, m_lServerId);
		m_pRecordBuilder->append(ITEM_ID, m_lItemId);
		m_pRecordBuilder->append(VALUE_TYPE, m_iValueType);
		m_pRecordBuilder->append(KEY_, m_strKey);
		m_pRecordBuilder->append(UNITS, m_strUnits);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CItemModel::DestroyData()
{
	ReleaseBuilder();
	m_iZbxServerId = m_iStatus = m_iValueType = 0;
	m_lClock = m_lTriggerId = m_lHostId = m_lItemId = m_lServerId = 0;
	m_strDescription = m_strKey = m_strUnits = "";
}