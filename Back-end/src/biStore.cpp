#include "./Controller/mysqlController.h"
#include "./Common/Common.h"
#include "./Common/DBCommon.h"

int main()
{
	ConnectInfo CInfo;
	CmysqlController objDBCtrl;
	CInfo.strHost = "10.40.9.240" ;
	CInfo.strUser = "root" ;
	CInfo.strPass = "P@ssWord123" ;
	CInfo.strSource = "sdk_master" ;
	CInfo.strPort = "" ;
	objDBCtrl.Connect(CInfo);
	while(true)
	{
		objDBCtrl.Query("CALL sp_mdr_TransportData");
		sleep(3600);
	}
	return 0;
}