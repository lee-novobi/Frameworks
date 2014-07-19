//#include "StdAfx.h"
#include "AlertController.h"
#include "../Common/DBCommon.h"

CAlertSyncController::CAlertSyncController(void)
{
	m_strTableName = ".monitoring_assistant_alerts";
}

CAlertSyncController::~CAlertSyncController(void)
{
}

void CAlertSyncController::Save()
{
	Insert(m_strTableName,*m_pModel);
}