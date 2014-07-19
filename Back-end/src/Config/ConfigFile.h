#pragma once
#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include "ConfigReader.h"

class CConfigFile:public CConfigReader
{
public:	
	CConfigFile(const string& strCfgFile);
	~CConfigFile(void);
	
	string GetErrorLog();
	string GetHost();
	string GetUser();
	string GetPassword();
	string GetSource();
	string GetPort();
	string GetReadReference();	
	bool IsReplicateSetUsed();	
};

