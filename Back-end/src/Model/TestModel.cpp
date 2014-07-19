#include "TestModel.h"

CTestModel::CTestModel()
{
}


/*CTestModel::CTestModel(int iZabbix_server_id, long long iHostid, const char* sHost, const char* sName, int iStatus, int iAvailable)
{
	zabbix_server_id = iZabbix_server_id;
	hostid = iHostid;
	host = sHost;
	name = sName;
	status = iStatus;
	available = iAvailable;
} */

void CTestModel::save(CMongodbController*& connDB,int zabbix_server_id, long long hostid, const char* host, const char* name, int status, int available)
{
	to.append( "zabbix_server_id" , zabbix_server_id );
    to.append( "hostid" , hostid );
	to.append( "host" , host );
    to.append( "name" , name );
	to.append( "status" , status );
    to.append( "available" , available );
	connDB->Insert("zabbix_master.test",to);
}

CTestModel::~CTestModel(void)
{
}
