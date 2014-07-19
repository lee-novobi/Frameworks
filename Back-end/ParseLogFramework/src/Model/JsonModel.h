#include "../Common/Common.h"
#include "json/json.h"
#include <json/value.h>

class CJsonModel
{
public:
	CJsonModel();
	~CJsonModel();
	bool AppendArray(string strJsonValue);
	void AppendValue(string strFieldNamme, string strJsonValue);
	void AppendValue(string strFieldNamme, int iJsonValue);
	//void AppendValue(string strFieldNamme, LargestUInt lJsonValue);
	string GetString(string strFieldNamme);
	int GetInt(string strFieldNamme);
	long long GetLong(string strFieldNamme);
	string toString();
	void DestroyData();
protected:
	Json::Value m_valRoot;
};