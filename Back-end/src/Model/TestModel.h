#pragma once
#include "MongodbModel.h"
#include "../Controller/MongodbController.h"
#include <string>

using namespace mongo;

class CTestModel:public CMongodbModel
{
public:
	CTestModel();
	//CTestModel(int iZabbix_server_id, long long iHostid, const char* sHost, const char* sName, int iStatus, int iAvailable);
	~CTestModel(void);

	/*virtual void serialize(BSONObjBuilder& to)
	{
		to << "zabbix_server_id" << zabbix_server_id << "hostid" << hostid 
			<< "host" << host << "name" << name  << "status" << status << "available" << available;
	}

    virtual void unserialize(const BSONObj& from)
	{		
		from["zabbix_server_id"].Val(zabbix_server_id);
		from["hostid"].Val(hostid);
		from["host"].Val(host);
		from["name"].Val(name);
		from["status"].Val(status);
		from["available"].Val(available);
	}*/

	void save(CMongodbController*& connDB,int zabbix_server_id, long long hostid, const char* host, const char* name, int status, int available);

protected:
	BSONObjBuilder to;
	/*int zabbix_server_id;
	long long hostid;
	const char* host;
	const char* name;
	int status;
	int available; */
};
