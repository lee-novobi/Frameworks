#include "CheckPingAction.h"
#include "ActionParser.h"

CCheckPingAction::CCheckPingAction()
{
}

CCheckPingAction::~CCheckPingAction()
{
}

MA_RESULT CCheckPingAction::Do()
{
	cout<<"CCheckPingAction"<<endl;
	FILE *fp;
	char cLine[1035];
	MA_RESULT pingResult;
	int nLen, nPos;
	/* Open the command for reading. */
	fp = popen("ping -c 1 127.0.0.1 | grep received", "r");
	//fp = popen(cmd, "r");
	if (fp == NULL) 
	{
		// Failed to run command
		cout<<"{}"<<endl;
		return MA_RESULT_FAIL;
	}
	fgets(cLine, sizeof(cLine)-1, fp);
 
	nLen = strlen(cLine);
	nPos = 0;
	string strPingRs;
 
	CActionParser::Get_token(cLine, nLen, nPos);
	CActionParser::Get_token(cLine, nLen, nPos);
	CActionParser::Get_token(cLine, nLen, nPos);
	strPingRs = CActionParser::Get_token(cLine, nLen, nPos);
	pingResult = (MA_RESULT)atoi(strPingRs.c_str());
	pclose(fp);
 
	return pingResult;
}
 



