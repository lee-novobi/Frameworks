#pragma once
#include <sys/time.h>
#include <stdio.h>
#include <mysql.h>
#include <string>
#include <iostream>
#include "../Common/Common.h"

using namespace std;

class CBaseModel;
struct ConnectInfo;

class CMySQLController
{
public:
	CMySQLController();
	CMySQLController(ConnectInfo CInfo);
	~CMySQLController();

	bool Connect(ConnectInfo CInfo);
	void SetQuery(string strQuery);
	
	int SelectQuery();
	bool SelectQuery(const char* strQuery);
	bool Query(const char* strQuery);
	void Save();
	void Update();
	void PrintResult();

	bool NextRow();
	
	// use to fetch row data
	string FetchString(string strField);
	int FetchInt(string strFieldName);
	long long FetchLong(string strFieldName);

protected:
	
	MYSQL m_Connection;
	
	MYSQL_RES *m_pResult;
	MYSQL_ROW m_oRow;
	MYSQL_FIELD *m_pFields;
	
	unsigned int m_iNumFields;
	
	CBaseModel *m_pModel;
	
	const char* m_strQuery;
	string m_strTableName;
	
	int GetColByFieldName(string strFieldName);
	//int state;
};
