#include "SyncProcessData.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"

CSyncProcessData::CSyncProcessData(const std::string& strFileName)
:CConfigFileParse(strFileName)
{	
}

CSyncProcessData::~CSyncProcessData(void)
{
}