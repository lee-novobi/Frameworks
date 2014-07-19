// ODAMongoFrameWork_Demo.cpp : Defines the entry point for the console application.
//


// #include "mongo/Controller/TriggerController.h"
// #include "mongo/LogParser/TriggerLogParser.h"
#include "Controller/TriggerController.h"
#include "LogParser/TriggerLogParser.h"
#include <ctime>
extern "C"
{
   #include <pthread.h>
}
#include <iostream>
using namespace std;
using namespace mongo;


int main()
{
	CTriggerLogParser *pTriggerParser = new CTriggerLogParser();
	while(true)
		sleep(100);
	cout<< "main 10"<<endl;
	delete pTriggerParser;
	
	
	
	return 0;
}

