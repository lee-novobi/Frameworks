#pragma once
#include "mongodbmodel.h"

class CHistoryUInt :
	public CMongodbModel
{
public:
	CHistoryUInt(string strNS);
	~CHistoryUInt(void);

	virtual void serialize(BSONObjBuilder& to)
	{
		to << "zabbix_server_id" << zabbix_server_id << "hostid" << hostid
			<< "itemid" << itemid << "clock" << clock << "value" << value;
	}

    virtual void unserialize(const BSONObj& from)
	{		
		from["zabbix_server_id"].Val(zabbix_server_id);
		from["hostid"].Val(hostid);
		from["itemid"].Val(itemid);
		from["clock"].Val(clock);
		from["value"].Val(value);
	}
protected:
	int zabbix_server_id;
	long long hostid;
	long long itemid;
	int clock;
	long long value;
};
