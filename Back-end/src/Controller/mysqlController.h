#pragma once
#include <sys/time.h>
#include <stdio.h>
#include <mysql.h>
#include <string>
#include <iostream>
#include "../Common/Common.h"
#include "../Model/JsonModel.h"
using namespace std;

class CBaseModel;
class CJsonModel;
struct ConnectInfo;

class CmysqlController
{
public:
	CmysqlController();
	~CmysqlController();

	bool Connect(ConnectInfo CInfo);
	bool SelectQuery(const char* strQuery);
	bool Query(const char* strQuery);
	void Save();
	void Update();
	bool FindDB();
	void PrintResult();
	bool NextRow();
	void GetFieldName();
	int GetRowCount();
	bool Reconnect();
	bool SetUtf8();
	int AffectedRows();
	
	void ModelAppend(string strName, int iVal);
	void ModelAppend(string strName, long long lVal);
	void ModelAppend(string strName, string strVal);
	void ModelAppend(string strName);
	
	long long ModelGetLong(string strFieldName);
	int ModelGetInt(string strFieldName);
	string ModelGetString(string strFieldName);
	void ResetModel();

protected:
	MYSQL_RES *m_pResult;
	MYSQL m_Connection;
	CBaseModel *m_pModel;
	CJsonModel m_objJsonModel;
	string m_strTableName;
	string m_strQuery;
	vector<string> m_vFieldName;
	//int state;
};
