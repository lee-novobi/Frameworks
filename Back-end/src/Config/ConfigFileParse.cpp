#include "ConfigFileParse.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"

CConfigFileParse::CConfigFileParse(const std::string& strFileName)
:CConfigFile(strFileName)
{	
}

CConfigFileParse::~CConfigFileParse(void)
{	
}

int CConfigFileParse::GetPosition()
{	
	return ReadIntValue(INFO,POS);
}

void CConfigFileParse::SetPosition(int iPosition)
{
	stringstream strPos;
	strPos<<iPosition;
	Update(INFO,POS,strPos.str());
}