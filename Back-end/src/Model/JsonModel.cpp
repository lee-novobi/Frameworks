#include "JsonModel.h"

CJsonModel::CJsonModel()
{
}

CJsonModel::~CJsonModel()
{
}

bool CJsonModel::AppendArray(string strJsonValue)
{
	bool bParsedSuccess;
	Json::Reader objReader;
	Json::Value valApp;
	
	try{
		bParsedSuccess = objReader.parse(strJsonValue, valApp, false);
	}
	
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	if(!bParsedSuccess){
		return false;
	}
	try{
		m_valRoot[m_valRoot.size()] = valApp;
		
		// string strFormatLog;
		// stringstream strErrorMess;
		// strErrorMess <<"AppendArray 5 : " << m_valRoot[(unsigned int)0]["Physical"]["Product"][(unsigned int)0].toStyledString() << endl;
		// strFormatLog = CUtilities::FormatLog(BUG_MSG, "MySQLController", "ModelGetString", strErrorMess.str());
		// CUtilities::WriteErrorLog(strFormatLog);
		
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return true;
}

void CJsonModel::AppendValue(string strFieldNamme, string strJsonValue)
{
	m_valRoot[strFieldNamme] = strJsonValue;
}

void CJsonModel::AppendValue(string strFieldNamme, int iJsonValue)
{
	m_valRoot[strFieldNamme] = iJsonValue;
}
/*
void CJsonModel::AppendValue(string strFieldNamme, LargestInt lJsonValue)
{
	m_valRoot[strFieldNamme] = lJsonValue;
}
*/
string CJsonModel::GetString(string strFieldNamme)
{
	return m_valRoot[strFieldNamme].asString();
}

int CJsonModel::GetInt(string strFieldNamme)
{
	return atoi(m_valRoot[strFieldNamme].asString().c_str());
}

long long CJsonModel::GetLong(string strFieldNamme)
{
	return atol(m_valRoot[strFieldNamme].asString().c_str());
}

string CJsonModel::toString()
{
	string strRes = "[]";
	try{
		if(!m_valRoot.empty()){
			strRes = m_valRoot.toStyledString();
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	
	return strRes;
}

Json::Value CJsonModel::parseValueRootJson(string strJsonValue)
{
	bool bParsedSuccess;
	Json::Reader objReader;
	Json::Value valApp;
	
	try{
		//bParsedSuccess = objReader.parse(strJsonValue, m_valRoot, false);
		bParsedSuccess = objReader.parse(strJsonValue, valApp, false);
	}
	catch(exception& ex)
	{	
		printf("4\n");
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return valApp;
}


// strErrorMess <<"AppendArray 5 : " << m_valRoot[(unsigned int)0]["Physical"]["Product"][(unsigned int)0].toStyledString() << endl;

string CJsonModel::toStringIndex(unsigned int iIndex){
	return m_valRoot[iIndex].toStyledString();
}
string CJsonModel::toStringKey(string strKey){
	return m_valRoot[strKey].toStyledString();
}

bool CJsonModel::GoToIndex(unsigned int iIndex)
{
	if(m_valRoot[iIndex].empty())
		return false;
	m_valRoot = m_valRoot[iIndex];
	return true;
}

bool CJsonModel::GoToKey(string strKey)
{
	if(m_valRoot[strKey].empty())
		return false;
	m_valRoot = m_valRoot[strKey];
	return true;
}


string CJsonModel::toString(unsigned int iIndex, string strKey)
{
	return m_valRoot[iIndex][strKey].toStyledString();
}
string CJsonModel::toString(string strKey, unsigned int iIndex)
{
	return m_valRoot[strKey][iIndex].toStyledString();
}

int CJsonModel::GetSize()
{
	return m_valRoot.size();
}
	
void CJsonModel::DestroyData()
{	
	if(!m_valRoot.empty())
		m_valRoot.clear();
}

//void CJsonModel::setValueRootJson(string strJsonValue)
//{
//	printf("1\n");
//	bool bParsedSuccess;
//	Json::Reader objReader;
//	Json::Value valApp;
//	
//	try{
//		printf("2\n");
//		//bParsedSuccess = objReader.parse(strJsonValue, m_valRoot, false);
//		bParsedSuccess = objReader.parse(strJsonValue, valApp, false);
//		m_valRoot = valApp;
//
//		printf("3\n");
//	}
//	catch(exception& ex)
//	{	
//		printf("4\n");
//		stringstream strErrorMess;
//		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
//		printf("%s", strErrorMess.str());
//		CUtilities::WriteErrorLog(strErrorMess.str());
//	}
//}