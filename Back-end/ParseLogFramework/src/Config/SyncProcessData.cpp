#include "SyncProcessData.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"

CSyncProcessData::CSyncProcessData(void)
{
}

CSyncProcessData::CSyncProcessData(string strInfoFile)
{
	m_oConfigReader = new CConfigReader(strInfoFile);
}

CSyncProcessData::~CSyncProcessData(void)
{
}

int CSyncProcessData::GetPosition()
{
	string line;
	line = GetData(INFO,POS);
	return atoi(line.c_str());
}

void CSyncProcessData::SetPosition(int iPosition)
{
	stringstream strPos;
	strPos<<iPosition;
	UpdateData(INFO,POS,strPos.str());
}