#include <sys/time.h>
#include <stdio.h>
#include <mysql.h>

class CmysqlConnector
{
public:
	CmysqlConnector();
	CmysqlConnector(const char* host, const char* usr, const char* pswd, const char* database);
	void GetResult();
	int Query(const char* strQuery);
	~CmysqlConnector();
protected:
	MYSQL_RES *result;
	MYSQL connection;
	int state;
};
