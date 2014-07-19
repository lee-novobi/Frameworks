// #include "Action/Custom/Check/PingAction.h"
#include "Action/Custom/Check/TelnetAction.h"
#include <iostream>

using namespace std;

int main()
{
	CTelnetAction *telnetAct = new CTelnetAction();
	bool rs = telnetAct->DoAct("127.0.0.1","80");
	if(rs)
		cout<<"Success"<<endl;
	else
		cout<<"Incident"<<endl;
	delete telnetAct;
	return 0;
}

