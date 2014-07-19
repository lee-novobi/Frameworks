#pragma once
#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include "../Common/Common.h"
#include "ConfigFile.h"

class CConfigFileParse:public CConfigFile
{
public:	
	CConfigFileParse(const std::string& strFileName);
	~CConfigFileParse(void);

	int GetPosition();
	void SetPosition(int iPosition);
};

