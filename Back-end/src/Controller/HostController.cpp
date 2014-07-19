#include "HostController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CHostController::CHostController(void)
{
}

CHostController::CHostController(string strDBName)
{
	m_strTableName = strDBName + ".host_mdr";
}

CHostController::~CHostController(void)
{
}


string CHostController::GetSelectQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "SELECT * FROM " + m_strTableName + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str();
	return strQuery;
}

string CHostController::GetInsertQuery()
{
	string strQuery;
	strQuery = "INSERT INTO " + m_strTableName + m_pModel->GetInsFormat();
	return strQuery;
}

string CHostController::GetUpdateQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "UPDATE " + m_strTableName + m_pModel->GetUpdFormat() + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str() + ";";
	return strQuery;
}

bool CHostController::FindOne()
{
	SelectQuery(GetSelectQuery().c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CHostController::Save()
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

void CHostController::Update()
{
	Query(GetUpdateQuery().c_str());
}