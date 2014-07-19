#pragma once

#include "../Common/Common.h"

struct BaseField
{
	string strName;

	union u_Value{
		int iVal;
		long long lVal;
		char* cVal;
	} Value;
};

class CBaseModel
{
protected:
	vector<BaseField> m_vRecord;
	string m_strInsName;
	string m_strInsVal;
	string m_strUpdFormat;
	string m_strSltFormat;
public:
	void AppendInt(string strName, int iVal);
	void AppendLong(string strName, long long lVal);
	void AppendString(string strName, string strVal);
	void AppendNow(string strName);
	int GetInt(string strName);
	long long GetLong(string strName);
	string GetString(string strName);
	string GetInsFormat();
	string GetUpdFormat();
	string GetSltFormat();
	
	void Reset();

	CBaseModel(void);
	~CBaseModel(void);
};

