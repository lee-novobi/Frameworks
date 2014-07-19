#include "ConfigFile.h"
#include "../Common/DBCommon.h"

CConfigFile::CConfigFile(const string& strFileName)
:CConfigReader(strFileName)
{	
}

CConfigFile::~CConfigFile(void)
{	
}

string CConfigFile::GetErrorLog()
{   
   return ReadStringValue("ERROR", "ErrorLog");;
}

string CConfigFile::GetHost()
{
	return ReadStringValue(MONGODB_MA, HOST);
}

string CConfigFile::GetUser()
{
	return ReadStringValue(MONGODB_MA, USER);
}

string CConfigFile::GetPassword()
{
	return ReadStringValue(MONGODB_MA, PASS);
}

string CConfigFile::GetSource()
{
	return ReadStringValue(MONGODB_MA, SRC);
}

string CConfigFile::GetPort()
{
	return ReadStringValue(MONGODB_MA, PORT);
}

bool CConfigFile::IsReplicateSetUsed()
{
	return ReadBoolValue(MONGODB_MA, REPLICA_SET);
}

string CConfigFile::GetReadReference()
{
	return ReadStringValue(MONGODB_MA, READ_REFERENCE);
}