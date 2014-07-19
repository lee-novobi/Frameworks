#pragma once
#include "HistoryLogParser.h"

class CHostInfoLogParser: public CHistoryLogParser
{
public:
	CHostInfoLogParser(string strCfgFile);
	CHostInfoLogParser();
	~CHostInfoLogParser();
protected:
	void ParseSystemInfo(const char* Buffer, int iPosition, int iLength, long long lServerId, map<long long,string> &mapHostMacDict);
	vector< InterfaceInfo > GetInterfaceInfo(const char* Buffer);
	string CalculateServerName(string strServerName);
	void CalculateInterface(string strInterfaceBlock, string& strJsonPriIP, string& strJsonPubIP, string& strMac);
};