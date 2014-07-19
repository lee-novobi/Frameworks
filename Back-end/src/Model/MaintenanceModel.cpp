#include "MaintenanceModel.h"

CMaintenanceModel::CMaintenanceModel(void)
{
}
CMaintenanceModel::~CMaintenanceModel(void)
{
}

/******************************************************************************
 *                                                                            *
 * Function: GetListMaintenanceInfo                                           *
 *                                                                            *
 * Purpose: returns Query object of all document maintenance                  *
 *                                                                            *
 ******************************************************************************/
Query CMaintenanceModel::GetListMaintenanceInfo()
{
	Query query = Query();
	return query;
}

/******************************************************************************
 *                                                                            *
 * Function: GetListHostMaintenanceInfo                                       *
 *                                                                            *
 * Purpose: returns Query object of all document maintenance host             *
 *                                                                            *
 ******************************************************************************/
Query CMaintenanceModel::GetListHostMaintenanceInfo(long long lMaintenanceId)
{
	BSONObj p = BSON("maintenanceid" << lMaintenanceId);
	Query query = Query(p);
	return query;
}

/******************************************************************************
 *                                                                            *
 * Function: GetMaintenanceAlertInfo										  *
 *                                                                            *
 * Purpose: returns Query object of all document maintenance host alert       *
 *                                                                            *
 ******************************************************************************/
Query CMaintenanceModel::GetMaintenanceAlertInfo(long long lHostId, int iMaintenanceStatus)
{
	BSONObj p = BSON("zabbix_server_id" << lHostId << "zbx_maintenance" << NE << iMaintenanceStatus);
	Query query = Query(p);
	return query;
}

void CMaintenanceModel::PrepareRecord()
{
}

void CMaintenanceModel::DestroyData()
{
}