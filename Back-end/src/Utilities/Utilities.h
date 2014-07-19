#include <iostream>
#include <fstream>
#include <stdio.h>
#include <stdlib.h>
#include <iconv.h>
#include <boost/algorithm/string.hpp>
#include <boost/algorithm/string/split.hpp>
#include <iterator> // for ostream_iterator
#include <vector>
#include <map>
#include <boost/regex.hpp>
#include <string>

using namespace std;


#ifndef UTILITIES_H
#define UTILITIES_H

class CConfigFile;

class CUtilities
{
	private:
		string m_strErrorFile;

	public: 
		CUtilities();
		virtual ~CUtilities();
		
		static void WriteErrorLog(CConfigFile* oConfig, const std::string& strErrorMsg);
		static void WriteErrorLog(const std::string& strErrorMsg);
		static void WriteDataLog(const std::string& strDataInfo);

		static string FormatDateSuffixHistory(struct tm tm);
		static string GetDateSuffixHistory(int iPeriod);
		static std::vector<int> GetListZabbixProcessId(string strPathDatePattern);
		static string GetStdoutFromCommand(string cmd);
		static string ReplaceString(string strSubject, const string& strSearch, const string& strReplace);
		static string VIMJsonParser(string VInfo);
		static unsigned long IpToLong(string strIp);
		static vector<string> SplitString(string strBuffer, string strSplit);
		static vector<string> GetIPAddressCorrectly(string strInterfaceInfo);
		static string GetMacAddressCorrectly(string strInterfaceInfo);
		static string RemoveBraces(string strWithBraces);
		static string GetMongoObjId(string strObjId);
		static string GetCurrTime();
		static string GetCurrTime(const char* pFormat);
		static string GetCurrTimeStamp();
		static string ToLowerString(string strText);
		static string ToUpperString(string strText);
		static string GetNameByWebKey(string strKey);
		static string GetStepNameByWebKey(string strKey);
		static string GetUnitByWebKey(string strKey);
		static string ReplaceBlockBracket(string strBlockValue);
		static string GetSuffixPartition(long long lClock, int iPartitionDay);
		static string FormatLog(string strType, string strProcessName, string strFunctionName, string strInfo);
		static long long UnixTimeFromString(string strTime);
		static string StripTags(string strHtml);
		static string SystemCall(string strCmd);
		static string ConvertIntToString(int number);
		static string ConvertLongToString(long number);
		static struct tm* GetLocalTime(time_t *rawtime);
		static char* EncodeUTF8(const char* strValue);
};

#endif //UTILITIES_H