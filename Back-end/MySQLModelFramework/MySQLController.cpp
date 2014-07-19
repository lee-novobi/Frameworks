#include "MySQLController.h"
#include <string.h>
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"

CMySQLController::CMySQLController()
{
	m_pModel = new CBaseModel();
	m_pResult = NULL;
}

CMySQLController::CMySQLController(ConnectInfo CInfo)
{
	bool bRes = false;
	try
	{
		bRes = Connect(CInfo);
		if (!bRes) 
		{
			sleep(10);
			bRes = Connect(CInfo);
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

CMySQLController::~CMySQLController()
{
	delete m_pModel;
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
	}
	mysql_close(&m_Connection);
}

bool CMySQLController::Connect(ConnectInfo CInfo)
{
	mysql_init(&m_Connection);

	if (!mysql_real_connect(&m_Connection, CInfo.strHost.c_str(), CInfo.strUser.c_str(), CInfo.strPass.c_str(), CInfo.strSource.c_str(), 0, NULL, CLIENT_MULTI_STATEMENTS))
	{
		return false;
	}
	return true;
}

void CMySQLController::SetQuery(string strQuery)
{
	m_strQuery = strQuery.c_str();
}

int CMySQLController::LoadRow()
{
	return 1;
}

int CMySQLController::SelectQuery()
{
	try{
		int state = mysql_query(&m_Connection, m_strQuery);
		////cout<<strQuery<<endl;
		if (state != 0)
		{
			printf(mysql_error(&m_Connection));
			return state;
		}
		m_pResult = mysql_store_result(&m_Connection);
		m_iNumFields = mysql_num_fields(m_pResult);
		m_pFields = mysql_fetch_fields(m_pResult);

		return state;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

int CMySQLController::GetColByFieldName(string strFieldName)
{
	for(int i = 0; i < m_iNumFields; i++)
	{
	  // printf("Field %u is %s\n", i, m_pFields[i].name);
	   if (strFieldName.compare(m_pFields[i].name) == 0)
	   {
			return i;
	   }
	} 
	return -1;
}

bool CMySQLController::SelectQuery(const char* strQuery)
{
	try{
		int state = mysql_query(&m_Connection, strQuery);
		if (state != 0)
		{
			//error
			return false;
		}
		m_pResult = mysql_store_result(&m_Connection);
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
		return false;
	}
}

bool CMySQLController::Query(const char* strQuery)
{
	try{
		int state = mysql_real_query(&m_Connection, strQuery, (unsigned int) strlen(strQuery));
		////cout<<strQuery<<endl;
		if (state != 0)
		{	
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() <<  endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
		return false;
	}
}

void CMySQLController::PrintResult()
{
	MYSQL_ROW row;
	unsigned int i = 0;
	while ((row = mysql_fetch_row(m_pResult)) != NULL){
        printf("%s\n",row[i] != NULL ?
        row[i] : "NULL"); 
	}
}

bool CMySQLController::NextRow()
{
	m_oRow = mysql_fetch_row(m_pResult);
	if(m_oRow == false || m_oRow == NULL)
		return false;
	return true;
}

string CMySQLController::FetchString(string strFieldName)
{
	int iCol = GetColByFieldName(strFieldName);
	string strValue = "";
	
	if (m_oRow[iCol] != NULL)
		strValue = m_oRow[iCol];
	
	return strValue;	
}

int CMySQLController::FetchInt(string strFieldName)
{
	int iCol = GetColByFieldName(strFieldName);
	if (m_oRow[iCol] != NULL)
		return atoi(m_oRow[iCol]);	
	return NULL;
}

long long CMySQLController::FetchLong(string strFieldName)
{
	MYSQL_FIELD *field;
	int iCol = GetColByFieldName(strFieldName);
	if (m_oRow[iCol] != NULL)
		return atol(m_oRow[iCol]);	
	return NULL;
}





