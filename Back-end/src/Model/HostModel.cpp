#include "HistoryUInt.h"

CHostModel::CHostModel(string strNS)
:CMongodbModel(strNS)
{
	code = '';
	server_type = 1;
	server_usage = 1;
	deleted = 0;
	os_version = '';
	os_type = '';
	productid = 0;
	numof_os_hdd = 0;
	numof_data_hdd = 0;
	interface = NULL;
	technical_owner = NULL;
	
}

CHostModel::~CHostModel(void)
{
}
