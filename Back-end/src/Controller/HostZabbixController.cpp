#include "HostZabbixController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CHostZabbixController::CHostZabbixController(void)
{
	m_strTableName = ".host_zabbix";
}

CHostZabbixController::CHostZabbixController(string strDBName)
{
	m_strTableName = ".host_zabbix";
}

CHostZabbixController::~CHostZabbixController(void)
{
}


string CHostZabbixController::GetSelectQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "SELECT * FROM " + m_strTableName + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str();
	return strQuery;
}

string CHostZabbixController::GetInsertQuery()
{
	string strQuery;
	strQuery = "INSERT INTO " + m_strTableName + m_pModel->GetInsFormat();
	return strQuery;
}

string CHostZabbixController::GetUpdateQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "UPDATE " + m_strTableName + m_pModel->GetUpdFormat() + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str() + ";";
	return strQuery;
}

bool CHostZabbixController::FindOne()
{
	SelectQuery(GetSelectQuery().c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CHostZabbixController::Save()
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

void CHostZabbixController::Update()
{
	Query(GetUpdateQuery().c_str());
}

void CHostZabbixController::ResetModel()
{
	m_pModel->Reset();
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
		m_pResult = NULL;
	}
}