#pragma once
#include "../Common/Common.h"
#include "ConfigFileParse.h"

class CSyncProcessData: public CConfigFileParse
{
public:	
	CSyncProcessData(const std::string& strFileName);
	~CSyncProcessData(void);
};