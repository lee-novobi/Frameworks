#include "Utilities.h"
#include "Config.h"

/*
 * Constructor - Assign a file name
 */
CUtilities::CUtilities()
{

}

/*
 * Destructor -
 */
CUtilities::~CUtilities()
{

}

void CUtilities::WriteErrorLog(CConfig* oConfig, const std::string& strErrorMsg)
{
	ofstream fErrorLog;
	string strErrorLog;
	strErrorLog = oConfig->GetErrorLog();
	fErrorLog.open(strErrorLog.c_str());
	fErrorLog << strErrorMsg << "\n";
	fErrorLog.close();
}