#pragma once
#include "../Common/Common.h"
#include "ConfigFileParse.h"

class CAlertProcessData: public CConfigFileParse
{
public:	
	CAlertProcessData(const std::string& strFileName);
	~CAlertProcessData(void);	
};

