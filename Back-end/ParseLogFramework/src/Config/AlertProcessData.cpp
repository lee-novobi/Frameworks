#include "AlertProcessData.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"

CAlertProcessData::CAlertProcessData(void)
{
}

CAlertProcessData::CAlertProcessData(string strInfoFile)
{
	m_oConfigReader = new CConfigReader(strInfoFile);
}

CAlertProcessData::~CAlertProcessData(void)
{
}

int CAlertProcessData::GetPosition()
{
	string line;
	line = GetData(INFO,POS);
	return atoi(line.c_str());
}

void CAlertProcessData::SetPosition(int iPosition)
{
	stringstream strPos;
	strPos<<iPosition;
	UpdateData(INFO,POS,strPos.str());
}