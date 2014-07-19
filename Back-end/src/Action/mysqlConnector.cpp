#include "mysqlConnector.h"
#include <iostream>
using namespace std;

CmysqlConnector::CmysqlConnector()
{
	
}

CmysqlConnector::CmysqlConnector(const char* host, const char* usr, const char* pswd, const char* database)
{
	mysql_init(&connection);
	state = 1;
	cout<<"connection start"<<endl;
	
	if (!mysql_real_connect(&connection,host,usr,pswd,database,0,0,0))
	{
		cout<<"connect is null !"<<endl;
		cout<<mysql_error(&connection)<<endl;
	}
	cout<<"connection end"<<endl;
}

int CmysqlConnector::Query(const char* strQuery)
{
	state = mysql_query(&connection, strQuery);
	if (state !=0)
	{
		printf(mysql_error(&connection));
		return state;
	}
	result = mysql_store_result(&connection);
	return state;
}

void CmysqlConnector::GetResult()
{
	MYSQL_ROW row;
	
	for ( int i = 0; i < result->row_count; ++i )
    {
        row = mysql_fetch_row(result);

        // In tat ca cac colume:
        for ( int col = 0; col < mysql_num_fields(result); ++col )
            cout <<  row[col] << ":";

        cout << endl;
    }
}

CmysqlConnector::~CmysqlConnector()
{
	if(state==0)
		mysql_free_result(result);
	mysql_close(&connection);
}

