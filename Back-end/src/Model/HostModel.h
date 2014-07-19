#pragma once
#include "mongodbmodel.h"

struct tagInterface
{
	char* ip;
	char* name;
	char* mac_address;
} Interface;

typedef tagTechnical_owner
{

} Technical_owner;

class CHostModel :
	public CMongodbModel
{
public:
	CHostModel(string strNS);
	~CHostModel(void);

	virtual void serialize(BSONObjBuilder& to)
	{
		to << "zabbix_server_id" << zabbix_server_id << "hostid" << hostid << "code" << code 
			<< "host" << host << "name" << name << "location" << location 
			<< "os_version" << os_version << "os_type" << os_type << "status" << status 
			<< "available" << available << "maintenance_status" << maintenance_status << "maintenance_from" << maintenance_from
			<< "server_type" << server_type << "server_usage" << server_usage << "deleted" << deleted 
			<< "productid" << productid << "interface" << interface << "zb_ip" << zb_ip 
			<< "created_date" << created_date << "last_updated" << last_updated << "zb_agent_version" << zb_agent_version 
			<< "splunk_agent_version" << splunk_agent_version << "hw_monitor_version" << hw_monitor_version << "cpu_type" << cpu_type 
			<< "memory" << memory << "os_hdd_type" << os_hdd_type << "numof_os_hdd" << numof_os_hdd 
			<< "os_hdd_raid_type" << os_hdd_raid_type << "data_hdd_type" << data_hdd_type << "numof_data_hdd" << numof_data_hdd 
			<< "data_hdd_raid_type" << data_hdd_raid_type << "technical_owner" << technical_owner;
	}

    virtual void unserialize(const BSONObj& from)
	{		
		from["zabbix_server_id"].Val(zabbix_server_id);
		from["hostid"].Val(hostid);
		from["code"].Val(code);
		from["host"].Val(host);
		from["name"].Val(name);
		from["location"].Val(location);
		from["os_version"].Val(os_version);
		from["os_type"].Val(os_type);
		from["status"].Val(status)
		from["available"].Val(available)
		from["maintenance_status"].Val(maintenance_status)
		from["maintenance_from"].Val(maintenance_from);
		from["server_type"].Val(server_type);
		from["server_usage"].Val(server_usage);
		from["deleted"].Val(deleted);
		from["productid"].Val(productid);
		from["interface"].Val(interface);
		from["zb_ip"].Val(zb_ip);
		from["created_date"].Val(created_date);
		from["last_updated"].Val(last_updated);
		from["zb_agent_version"].Val(zb_agent_version);
		from["splunk_agent_version"].Val(splunk_agent_version);
		from["hw_monitor_version"].Val(hw_monitor_version);
		from["cpu_type"].Val(cpu_type);
		from["memory"].Val(memory);
		from["os_hdd_type"].Val(os_hdd_type);
		from["numof_os_hdd"].Val(numof_os_hdd);
		from["os_hdd_raid_type"].Val(os_hdd_raid_type);
		from["data_hdd_type"].Val(data_hdd_type);
		from["numof_data_hdd"].Val(numof_data_hdd);
		from["data_hdd_raid_type"].Val(data_hdd_raid_type);
		from["technical_owner"].Val(technical_owner);
	}
protected:
	int zabbix_server_id;
	long long hostid;
	char* code;
	char* host;
	char* name;
	char* location;
	char* os_version;
	char* os_type;
	int status;
	int available;
	int maintenance_status;
	int maintenance_from;
	int server_type;
	int server_usage;
	int deleted;
	int productid;
	Interface interface;
	char* zb_ip;
	int created_date;
	int last_updated;
	char* zb_agent_version;
	char* splunk_agent_version;
	char* hw_monitor_version;
	char* cpu_type;
	char* memory;
	char* os_hdd_type;
	int numof_os_hdd;
	int os_hdd_raid_type;
	char* data_hdd_type;
	int numof_data_hdd;
	int data_hdd_raid_type;
	Technical_owner technical_owner;
};
