// #include "Action/Custom/Check/PingAction.h"
#include "Action/CheckCCUAction.h"
#include <iostream>

using namespace std;

int main(int argc, char* argv[])
{
	//2013,2,28,24,0,0 2013,2,27,22,30,0 "1361979000" "CN772902A7"
	cout<<argv[1]<<endl;
	CCheckCCUAction *CheckCCUAct = new CCheckCCUAction(argv[1],argv[2],atoi(argv[3]));
	CheckCCUAct->Do();
	delete CheckCCUAct;
	return 0;
}