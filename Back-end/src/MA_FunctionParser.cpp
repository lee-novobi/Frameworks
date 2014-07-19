// ODAMongoFrameWork_Demo.cpp : Defines the entry point for the console application.
//


// #include "mongo/Controller/EventController.h"
// #include "mongo/LogParser/EventLogParser.h"
#include "Controller/FunctionController.h"
#include "LogParser/FunctionLogParser.h"
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
	CFunctionLogParser *pFunctionParser = new CFunctionLogParser();
	while(true)
		sleep(100);
	cout<< "main 100"<<endl;
	delete pFunctionParser;
	
	return 0;
}

