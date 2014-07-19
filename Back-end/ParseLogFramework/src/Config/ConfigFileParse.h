#pragma once
#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include "../Common/Common.h"
#include "Config.h"

class CConfigFileParse:public CConfig
{
public:
	CConfigFileParse(void);
	CConfigFileParse(string strCfgFile);
	~CConfigFileParse(void);
};

