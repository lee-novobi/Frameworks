#include <stdio.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>

#include <iostream>
using namespace std;

int main(int argc, char *argv[])
{
	string strKeyValue;
	int iFound;

	strKeyValue = "Description";
	iFound = strKeyValue.find("ERROR");
	
	printf("Exist Position:%d\n", iFound);
	
	return 0;
}
