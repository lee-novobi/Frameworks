#include "mysqlController.h"
#include <string.h>
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"

ConnectInfo g_CMysqlInfo;
CmysqlController::CmysqlController()
{
	m_pModel = new CBaseModel();
	// m_objJsonModel = new CJsonModel();
	m_pResult = NULL;
}

CmysqlController::~CmysqlController()
{
	delete m_pModel;
	// delete m_objJsonModel;
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
	}
	mysql_close(&m_Connection);
}

bool CmysqlController::Connect(ConnectInfo CInfo)
{
	m_strTableName = CInfo.strSource + m_strTableName;
	g_CMysqlInfo = CInfo;
	mysql_init(&m_Connection);
	
	if (!mysql_real_connect(&m_Connection,CInfo.strHost.c_str(),CInfo.strUser.c_str(),CInfo.strPass.c_str(),CInfo.strSource.c_str(),0,NULL,CLIENT_MULTI_STATEMENTS))
	{
		cout<<"m_Connection false"<<endl;
		return false;
	}
	SetUtf8();
	return true;
}

bool CmysqlController::Reconnect()
{
	
	mysql_close(&m_Connection);
	
	mysql_init(&m_Connection);
	if (!mysql_real_connect(&m_Connection, g_CMysqlInfo.strHost.c_str(), g_CMysqlInfo.strUser.c_str(), g_CMysqlInfo.strPass.c_str(), g_CMysqlInfo.strSource.c_str(), 0, NULL, CLIENT_MULTI_STATEMENTS))
	{
		CUtilities::WriteErrorLog(mysql_error(&m_Connection));
		return false;
	}
	SetUtf8();
	return true;
}

int CmysqlController::AffectedRows(){
	return mysql_affected_rows(&m_Connection);
}

bool CmysqlController::SelectQuery(const char* strQuery)
{
	try{
		if(m_pResult!=NULL)
		{
			mysql_free_result(m_pResult);
		}
		m_strQuery = strQuery;
		int state = mysql_query(&m_Connection, strQuery);
		////cout<<strQuery<<endl;
		if (state != 0)
		{
			CUtilities::WriteErrorLog(mysql_error(&m_Connection));
			return false;
		}
		m_pResult = mysql_store_result(&m_Connection);
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

bool CmysqlController::Query(const char* strQuery)
{
	try{
		m_strQuery = strQuery;
		int state = mysql_real_query(&m_Connection, strQuery, (unsigned int) strlen(strQuery));
		////cout<<strQuery<<endl;
		if (state != 0)
		{	
			CUtilities::WriteErrorLog(mysql_error(&m_Connection));
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

bool CmysqlController::FindDB()
{
	string strQuery = "SELECT * FROM " + m_strTableName;
	SelectQuery(strQuery.c_str());	
	if(m_pResult->row_count==0)
	{
		return false;
	}
	return true;
}

void CmysqlController::PrintResult()
{
	MYSQL_ROW row;
	for ( int i = 0; i < m_pResult->row_count ; i++ )
    {
        row = mysql_fetch_row(m_pResult);
        // In tat ca cac colume:
        for ( int col = 0; col < mysql_num_fields(m_pResult) ; col++ )
		{
			//cout<<row[col]<< " | ";
		}
		//cout<<endl;
    }
}

void CmysqlController::GetFieldName()
{
	MYSQL_FIELD *field;
	m_vFieldName.clear();
	for (int col = 0; col < mysql_num_fields(m_pResult) ; col++ )
	{
		field = mysql_fetch_field(m_pResult);
		m_vFieldName.push_back(field->name);
	}
	SelectQuery(m_strQuery.c_str());
}

int CmysqlController::GetRowCount()
{
	return m_pResult->row_count;
}

bool CmysqlController::NextRow()
{
	MYSQL_ROW row;
	MYSQL_FIELD *field;
	//cout<<"==================================shit : " << m_pResult->row_count << endl;
	row = mysql_fetch_row(m_pResult);
	if(row == false)
		return false;
	m_objJsonModel.DestroyData();
	for (int col = 0; col < mysql_num_fields(m_pResult) ; col++ )
	{
		if(row[col] != NULL)
		{
			m_objJsonModel.AppendValue(m_vFieldName[col], row[col]);
			//cout<<row[col]<< " | ";
		}
		
	}
	//cout<<endl;
	return true;
}

long long CmysqlController::ModelGetLong(string strFieldName)
{
	return m_objJsonModel.GetLong(strFieldName);
}

int CmysqlController::ModelGetInt(string strFieldName)
{
	return m_objJsonModel.GetInt(strFieldName);
}

string CmysqlController::ModelGetString(string strFieldName)
{
	return m_objJsonModel.GetString(strFieldName);
}

void CmysqlController::ModelAppend(string strName, int iVal)
{
	m_pModel->AppendInt(strName,iVal);
}

void CmysqlController::ModelAppend(string strName, long long lVal)
{
	m_pModel->AppendLong(strName,lVal);
}

void CmysqlController::ModelAppend(string strName, string strVal)
{
	m_pModel->AppendString(strName,strVal);
}

void CmysqlController::ModelAppend(string strName)
{
	m_pModel->AppendNow(strName);
}

void CmysqlController::ResetModel()
{
	m_pModel->Reset();
	m_strQuery = "";
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
		m_pResult = NULL;
	}
}


bool CmysqlController::SetUtf8()
{
	return Query("SET NAMES utf8");
}


