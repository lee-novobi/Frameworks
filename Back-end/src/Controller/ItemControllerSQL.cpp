#include "ItemController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"

CItemController::CItemController(void)
{
}

CItemController::CItemController(string strDBName)
{
	m_strTableName = strDBName + ".items";
}

CItemController::~CItemController(void)
{
}

string CItemController::GetSelectQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_iServerId;
	strQuery = "SELECT * FROM " + m_strTableName + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str();
	strTmp.str(string());
	strTmp << m_lItemId;
	strQuery += " AND ";
	strQuery += ITEM_ID;
	strQuery += " = " + strTmp.str();
	return strQuery;
}

string CItemController::GetInsertQuery()
{
	string strQuery;
	strQuery = "INSERT INTO " + m_strTableName + m_pModel->GetInsFormat();
	return strQuery;
}

string CItemController::GetUpdateQuery()
{
	string strQuery;
	stringstream strTmp;
	strTmp << m_iServerId;
	strQuery = "UPDATE " + m_strTableName + " WHERE ";
	strQuery += SERVER_ID;
	strQuery += " = " + strTmp.str();
	strTmp.str(string());
	strTmp << m_lItemId;
	strQuery += " AND ";
	strQuery += ITEM_ID;
	strQuery += " = " + strTmp.str();
	return strQuery;
}

bool CItemController::FindOne()
{
	SelectQuery(GetSelectQuery().c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CItemController::Save()
{
	string InsertQuery = GetInsertQuery();
	SelectQuery(GetSelectQuery().c_str());
	//cout<<InsertQuery<<endl;
	if(m_pResult->row_count==0)
	{
		Query(InsertQuery.c_str());
	}
}

void CItemController::Update()
{
	Query(GetUpdateQuery().c_str());
}