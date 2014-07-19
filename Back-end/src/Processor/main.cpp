
// #include "mongo/Action/Custom/Check/PingAction.h"
// #include "mongo/Action/Custom/Check/TelnetAction.h"
#include "FunctionExecuter.h"
#include "../Workflow/WorkFlowLoader.h"
#include "../Common/Common.h"
#include<unistd.h>

int main()
{
	cout<<"Hello baby !!"<<endl;
	CWorkFlowLoader *pWLObj = new CWorkFlowLoader();
	pWLObj->LoadWorkFlow();
	
	sleep(10);
	cout<< "main 10"<<endl;
	delete pWLObj;
	sleep(5);
	cout<< "return 10"<<endl;
	return 0;
}