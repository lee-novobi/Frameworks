#include "AlertProcessData.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"

CAlertProcessData::CAlertProcessData(const std::string& strFileName)
:CConfigFileParse(strFileName)
{	
}

CAlertProcessData::~CAlertProcessData(void)
{
}