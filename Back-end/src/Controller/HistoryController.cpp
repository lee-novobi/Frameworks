#include "HistoryController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CHistoryController::CHistoryController(void)
{
}

CHistoryController::CHistoryController(string strDBName)
{
	m_strTableName = strDBName + ".host_zabbix";
}

CHistoryController::~CHistoryController(void)
{
}


string CHistoryController::GetSelectQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "SELECT * FROM " + m_strTableName + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str();
	return strQuery;
}

string CHistoryController::GetInsertQuery()
{
	string strQuery;
	strQuery = "INSERT INTO " + m_strTableName + m_pModel->GetInsFormat();
	return strQuery;
}

string CHistoryController::GetUpdateQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_lServerId;
	strQuery = "UPDATE " + m_strTableName + m_pModel->GetUpdFormat() + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str() + ";";
	return strQuery;
}

bool CHistoryController::FindOne()
{
	SelectQuery(GetSelectQuery().c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CHistoryController::Save()
{
	string InsertQuery = GetInsertQuery();
	SelectQuery(GetSelectQuery().c_str());
	////cout<<InsertQuery<<endl;
	if(m_pResult->row_count==0)
	{
		Query(InsertQuery.c_str());
	}
}

void CHistoryController::Update()
{
	string strUpdateQuery = GetUpdateQuery().c_str();
	////cout << endl << strUpdateQuery << endl;
	Query(strUpdateQuery.c_str());
}

void CHistoryController::ResetModel()
{
	m_pModel->Reset();
}
