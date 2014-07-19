//#include "StdAfx.h"
#include "AlertACKController.h"
#include "../ExternalService/CurlService.h"
#include "../Common/DBCommon.h"

CAlertACKController::CAlertACKController(void)
{
	m_strTableName = ".alerts_ack";
}

CAlertACKController::~CAlertACKController(void)
{
}


bool CAlertACKController::CloneACK(string strNewObjId)
{
	if(m_bIsConnected)
	{
		while(NextRecord())
		{
			BSONObj bRecord;
			BSONObjBuilder bbRecordBuilder;
			m_CurrResultObj = m_CurrResultObj.removeField("_id");
			m_CurrResultObj = m_CurrResultObj.removeField("source_id");
			bRecord = m_CurrResultObj.removeField("alert_id");
			bbRecordBuilder.appendElements(bRecord);
			bbRecordBuilder.append("alert_id",OID(strNewObjId));
			bbRecordBuilder.append("source_id",OID(strNewObjId));
			InsertDB(BSONObj(), bbRecordBuilder.obj());
		}
	}
	return true;
}
