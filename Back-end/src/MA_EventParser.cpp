// ODAMongoFrameWork_Demo.cpp : Defines the entry point for the console application.
//


// #include "mongo/Controller/EventController.h"
// #include "mongo/LogParser/EventLogParser.h"
#include "Controller/EventController.h"
#include "LogParser/EventLogParser.h"
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
	CEventLogParser *pEventParser = new CEventLogParser();
	while(true)
		sleep(100);
	cout<< "main 10"<<endl;
	delete pEventParser;
	
	return 0;
}

