#include "ConfigFileParse.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"

CConfigFileParse::CConfigFileParse(void)
{
}

CConfigFileParse::CConfigFileParse(string strCfgFile)
{
	m_oConfigReader = new CConfigReader(strCfgFile);
}

CConfigFileParse::~CConfigFileParse(void)
{
	delete m_oConfigReader;
}

