//------------------------------------
#include "CSServiceController.h"
//------------------------------------
#include <ctime>
#include <stdio.h>
#include <stdlib.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <errno.h>
#include <unistd.h>
#include <pthread.h>
#include <dirent.h>

using namespace std;
//------------------------------------


int main(int argc, char* argv[])
{
	CCSIncident objCSIncident;
	string strResponse;

	strResponse = objCSIncident.UpdateStatusINC();
	cout << strResponse << endl;
	return 0;

}