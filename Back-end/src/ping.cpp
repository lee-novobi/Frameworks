#include <stdio.h>
// #include <sys/socket.h>
// #include <sys/ioctl.h>
// #include <sys/stat.h>
// #include <linux/netdevice.h>
// #include <arpa/inet.h>
// #include <netinet/in.h>
// #include <unistd.h>
// #include <errno.h>
#include <string.h>
#include <string>
// #include <map>
#include <algorithm>
#include <iostream>
#include "./Action/Action.h"
#include "./Action/CheckPingAction.h"
#include "./Common/Common.h"
using namespace std;

int main()
{
	MA_RESULT rs;
	CAction *Action = new CCheckPingAction();
	rs = Action->Do();
	if(rs == MA_RESULT_SUCCESS)
		cout<<"Success"<<endl;
	else
		cout<<"Incident"<<endl;
	delete Action;
	return (int)rs;
}


