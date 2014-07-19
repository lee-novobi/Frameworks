#include "PingAction.h"
#include "../../ActionParser.h"
CPingAction::CPingAction()
{
}

CPingAction::~CPingAction()
{
}

bool CPingAction::DoAct(const char* cmd)
{
	FILE *fp;
	char line[1035];

	/* Open the command for reading. */
	// fp = popen("ping -c 1 127.0.0.1 | grep received", "r");
	fp = popen(cmd, "r");
	if (fp == NULL) 
	{
		// Failed to run command
		cout<<"{}"<<endl;
		return false;
	}
	fgets(line, sizeof(line)-1, fp);
  
	int nLen = strlen(line);
	int nPos = 0;
	string strPingRs;
  
	CActionParser::Get_token(line, nLen, nPos);
	CActionParser::Get_token(line, nLen, nPos);
	CActionParser::Get_token(line, nLen, nPos);
	strPingRs = CActionParser::Get_token(line, nLen, nPos);
	int pingResult = atoi(strPingRs.c_str());
	pclose(fp);
  
	if(pingResult != 0)
	{
		return true;
	}
  
	return false;
}
 



