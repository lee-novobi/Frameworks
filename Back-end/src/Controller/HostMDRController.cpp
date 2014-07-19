#include "HostMDRController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CHostMDRController::CHostMDRController(void)
{
	m_strTableName = ".host_mdr";
}

CHostMDRController::CHostMDRController(string strDBName)
{
	m_strTableName = ".host_mdr";
}

CHostMDRController::~CHostMDRController(void)
{
}


string CHostMDRController::GetSelectQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "SELECT * FROM " + m_strTableName + " WHERE ";
	strQuery += ZBX_SERVERID;
	strQuery += " = " + strTmp.str();
	return strQuery;
}

string CHostMDRController::GetInsertQuery()
{
	string strQuery;
	strQuery = "INSERT INTO " + m_strTableName + m_pModel->GetInsFormat();
	return strQuery;
}

string CHostMDRController::GetUpdateQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "UPDATE " + m_strTableName + m_pModel->GetUpdFormat() + " WHERE ";
	strQuery += ZBX_SERVERID;
	strQuery += " = " + strTmp.str() + ";";
	return strQuery;
}

bool CHostMDRController::FindOne()
{
	SelectQuery(GetSelectQuery().c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CHostMDRController::Save()
{
	string InsertQuery = GetInsertQuery();
	SelectQuery(GetSelectQuery().c_str());
	////cout<<InsertQuery<<endl;
	if(m_pResult->row_count==0)
	{
		Query(InsertQuery.c_str());
	}
	else
		Query(GetUpdateQuery().c_str());
}

void CHostMDRController::Update()
{
	Query(GetUpdateQuery().c_str());
}

void CHostMDRController::ResetModel()
{
	m_pModel->Reset();
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
		m_pResult = NULL;
	}
}