#include "TopMaintenanceModel.h"

CTopMaintenanceModel::CTopMaintenanceModel()
{
	Init();
}
CTopMaintenanceModel::~CTopMaintenanceModel()
{
	DestroyData();
}
void CTopMaintenanceModel::Init()
{
}

void CTopMaintenanceModel::PrepareRecord()
{
	try{
		BSONArrayBuilder baObj;
		for(int i = 0; i < m_vtMaintenanceId.size(); i++)
		{
			baObj.append(m_vtMaintenanceId[i]);
		}
		m_pRecordBuilder->append("clock", m_iClock);
		m_pRecordBuilder->append("maintenanceid", baObj.arr());
	}catch(exception& ex)
	{	
		string strFormatLog;
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ <<endl;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CTopMaintenanceModel", "PrepareRecord", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
}
void CTopMaintenanceModel::DestroyData()
{
	ReleaseBuilder();
	Init();
}

Query CTopMaintenanceModel::QueryMaintenanceByClock(int iClock)
{
	BSONObj p = BSON("clock" << iClock);
	Query query = Query(p);
	return query; 
}

Query CTopMaintenanceModel::QueryMaintenanceWithOutScope(int iTimeLimit)
{
	BSONObj p = BSON("clock" << LT << iTimeLimit);
	Query query = Query(p);
	return query; 
}