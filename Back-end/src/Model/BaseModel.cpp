#include "BaseModel.h"


CBaseModel::CBaseModel(void)
{
}


CBaseModel::~CBaseModel(void)
{
}

void CBaseModel::AppendInt(string strName, int iVal)
{
	stringstream strTemp;
	BaseField bfTemp;
	bfTemp.strName = strName;
	bfTemp.Value.iVal = iVal;
	m_vRecord.push_back(bfTemp);

	strTemp << iVal;
	m_strInsName += strName ;
	m_strInsName += ", " ;
	
	m_strInsVal += strTemp.str() ;
	m_strInsVal += ", " ;

	m_strUpdFormat += strName + "=" + strTemp.str();
	m_strUpdFormat += ", ";

	m_strSltFormat += strName + "=" + strTemp.str();
	m_strSltFormat += " AND ";

}

void CBaseModel::AppendLong(string strName, long long lVal)
{
	stringstream strTemp;
	BaseField bfTemp;
	bfTemp.strName = strName;
	bfTemp.Value.lVal = lVal;
	m_vRecord.push_back(bfTemp);

	strTemp << lVal;
	m_strInsName += strName ;
	m_strInsName += ", " ;
	
	m_strInsVal += strTemp.str() ;
	m_strInsVal += ", " ;

	m_strUpdFormat += strName + "=" + strTemp.str();
	m_strUpdFormat += ", ";

	m_strSltFormat += strName + "=" + strTemp.str();
	m_strSltFormat += " AND ";
}

void CBaseModel::AppendString(string strName, string strVal)
{
	//BaseField bfTemp;
	//bfTemp.strName = strName;
	//bfTemp.Value.cVal = strVal.c_str();
	//m_vRecord.push_back(bfTemp);

	m_strInsName += strName;
	m_strInsName += ", " ;
	
	m_strInsVal += "'" + strVal + "'";
	m_strInsVal += ", " ;

	m_strUpdFormat += strName + "='" + strVal + "'";
	m_strUpdFormat += ", ";

	m_strSltFormat += strName + "='" + strVal + "'";
	m_strSltFormat += " AND ";
}

void CBaseModel::AppendNow(string strName)
{
	//BaseField bfTemp;
	//bfTemp.strName = strName;
	//bfTemp.Value.cVal = strVal.c_str();
	//m_vRecord.push_back(bfTemp);

	m_strInsName += strName;
	m_strInsName += ", " ;
	
	m_strInsVal += "NOW()";
	m_strInsVal += ", " ;

	m_strUpdFormat += strName + " = NOW()";
	m_strUpdFormat += ", ";

	m_strSltFormat += strName + " = NOW()";
	m_strSltFormat += " AND ";
}

void CBaseModel::Reset()
{
	m_strInsName = m_strInsVal = m_strUpdFormat = m_strSltFormat = "";
}

string CBaseModel::GetInsFormat()
{
	try{
		int iFound;
		string strInsert;
		strInsert = "(";
		iFound = m_strInsName.find_last_of(",");
		strInsert +=  m_strInsName.substr(0,iFound);
		strInsert += ") VALUES (";
		iFound = m_strInsVal.find_last_of(",");
		strInsert += m_strInsVal.substr(0,iFound);
		strInsert += ");";
		m_strInsName = "";
		m_strInsVal = "";
		return strInsert;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

string CBaseModel::GetUpdFormat()
{
	try{
	int iFound;
	string strUpdate;
	strUpdate = " SET ";
	iFound = m_strUpdFormat.find_last_of(",");
	strUpdate +=  m_strUpdFormat.substr(0,iFound);
	m_strUpdFormat = "";
	return strUpdate;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

string CBaseModel::GetSltFormat()
{
	try{
		int iFound;
		string strSelect;
		iFound = m_strSltFormat.find_last_of("AND");
		strSelect =  m_strSltFormat.substr(0,iFound-2);
		m_strSltFormat = "";
		return strSelect;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}