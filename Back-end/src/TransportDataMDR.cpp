#include "./Controller/mysqlController.h"
#include "./Common/Common.h"
#include "./Common/DBCommon.h"
#include "./Config/ConfigFileParse.h"
int main()
{
	int child_id;
	child_id = fork();
	if (child_id) {
		cout << "I'm parent of " << child_id << endl;
	}
	else {
		ConnectInfo CInfo;
		CmysqlController objDBCtrl;
		CConfigFileParse *m_pConfigFile = new CConfigFileParse("./ParserQT.ini");
		
		CInfo.strHost = m_pConfigFile->GetData(MYSQL_MDR,HOST);
		CInfo.strUser = m_pConfigFile->GetData(MYSQL_MDR,USER);
		CInfo.strPass = m_pConfigFile->GetData(MYSQL_MDR,PASS);
		CInfo.strSource = m_pConfigFile->GetData(MYSQL_MDR,SRC);
		
		objDBCtrl.Connect(CInfo);
		while(true)
		{
			objDBCtrl.Query("CALL sp_mdr_TransportData");
			sleep(3600);
		}
	}
	
	return 0;
}