#include "ImpactLevelModel.h"
#include "../Common/DBCommon.h"
CImpactLevelModel::CImpactLevelModel(void)
{
}

CImpactLevelModel::~CImpactLevelModel(void)
{
}

Query CImpactLevelModel::GetImpactLevelByCaseNumQuery()
{
	Query queryQueryResult = QUERY(SOURCE_FROM<<m_strSourceForm
				<<INC_CASE_FROM<<LTE<<m_iNumOfCase
				<<INC_CASE_TO<<GT<<m_iNumOfCase);
	return queryQueryResult;
}

void CImpactLevelModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(SOURCE_FROM, m_strSourceForm);
		m_pRecordBuilder->append(NUM_OF_CASE, m_iNumOfCase);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CImpactLevelModel::DestroyData()
{
	ReleaseBuilder();
	m_strSourceForm = "";
	m_iNumOfCase = 0;
}