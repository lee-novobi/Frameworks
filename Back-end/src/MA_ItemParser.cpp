// ODAMongoFrameWork_Demo.cpp : Defines the entry point for the console application.
//


// #include "mongo/Controller/EventController.h"
// #include "mongo/LogParser/EventLogParser.h"
#include "Controller/ItemController.h"
#include "LogParser/ItemLogParser.h"
#include "Common/Common.h"
#include <ctime>
extern "C"
{
   #include <pthread.h>
}
#include <iostream>
using namespace std;
bool CheckDestroy(string strCfgFile);

int main()
{
	CItemLogParser *pItemParser = new CItemLogParser("ParserHL.ini");
	while(true)
		{
			if(!CheckDestroy("CheckStopProc"))
			{
				delete pItemParser;
				sleep(2);
				break;
			}
			sleep(10);
		}
	return 0;
}

bool CheckDestroy(string strCfgFile)
{
	ifstream fConfig;
	string strLine;
	fConfig.open(strCfgFile.c_str());
	if(fConfig.is_open())
	{
		getline(fConfig,strLine);
		if(strLine.compare("true") != 0)
		{
			cout<<"Destroy !! \n";
			fConfig.close();
			return false;
		}
	}
	return true;
}