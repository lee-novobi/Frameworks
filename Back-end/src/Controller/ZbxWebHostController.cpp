#include "ZbxWebHostController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CZbxWebHostController::CZbxWebHostController(void)
{
}

CZbxWebHostController::CZbxWebHostController(string strDBName)
{
	m_strTableName = strDBName + ".hosts";
}

CZbxWebHostController::~CZbxWebHostController(void)
{
}

bool CZbxWebHostController::FindDB()
{
	string strQuery = "SELECT * FROM " + m_strTableName;
	SelectQuery(strQuery.c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CZbxWebHostController::ResetModel()
{
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
		m_pResult = NULL;
	}
}
