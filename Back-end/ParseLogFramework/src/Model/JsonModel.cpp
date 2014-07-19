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
	if(!bParsedSuccess)
		return false;
	try{
		m_valRoot[m_valRoot.size()] = valApp;
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

void CJsonModel::DestroyData()
{	
	if(!m_valRoot.empty())
		m_valRoot.clear();
}

