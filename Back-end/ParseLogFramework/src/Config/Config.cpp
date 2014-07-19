#include "Config.h"
#include "ConfigReader.h"

/*
 * Constructor - Assign a file name
 */
CConfig::CConfig()
{
}

/*
 * Destructor -
 */
CConfig::~CConfig()
{
}

string CConfig::GetErrorLog()
{
   string strLogFile;
   strLogFile = m_oConfigReader->load("ERROR", "ErrorLog");
   return strLogFile;
}

string CConfig::GetData(string strGroupName, string strPropertise)
{
	return m_oConfigReader->load(strGroupName,strPropertise);
}

void CConfig::AddData(string strGroupName, string strPropertise, string strValue)
{
	m_oConfigReader->add(strGroupName,strPropertise, strValue);
}

void CConfig::UpdateData(string strGroupName, string strPropertise, string strValue)
{
	m_oConfigReader->update(strGroupName,strPropertise, strValue);
}