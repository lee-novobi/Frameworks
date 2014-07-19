// #include "mongo/Action/Custom/Check/PingAction.h"
#include "mongo/Action/Custom/Check/TelnetAction.h"
#include <iostream>

using namespace std;

int main()
{
	// CPingAction *pingAct = new CPingAction();
	// bool rs = pingAct->DoAct("ping -c 1 127.0.0.1 | grep received");
	
	//Connect mongoDB
		//get Event
	//
	//PortDown
		//get host_name
		//get trigger_id
		//get workflow : Array Action
		
		//ping host
		
		//CheckisGameAction(host_name)
		//true
			//CheckLoginAction
				//true --> telnet
					//true
					//false --> incident
				//false --> incident
		//false
			//telnet
				//true
				//false --> incident
	
	CTelnetAction *telnetAct = new CTelnetAction();
	bool rs = telnetAct->DoAct("127.0.0.1","80");
	if(rs)
		cout<<"Success"<<endl;
	else
		cout<<"Incident"<<endl;
	delete telnetAct;
	return 0;
}