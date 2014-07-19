#include "MapProductModel.h"
#include "../Common/DBCommon.h"
CMapProductModel::CMapProductModel(void)
{
}

CMapProductModel::~CMapProductModel(void)
{
}

Query CMapProductModel::GetMapProductBySrcProductQuery()
{
	Query queryQueryResult = QUERY(MAP_SOURCE<<m_strMapSrc<<MAP_SRC_PRODUCT<<m_strMapProdSrc);
	return queryQueryResult;
}

void CMapProductModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(MAP_SOURCE, m_strMapSrc);
		m_pRecordBuilder->append(MAP_SRC_PRODUCT, m_strMapProdSrc);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CMapProductModel::DestroyData()
{
	ReleaseBuilder();
	m_strMapSrc = m_strMapProdSrc = "";
}