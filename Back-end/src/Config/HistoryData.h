#pragma once
#include <iostream>
#include "LogParserData.h"

using namespace std;

#ifndef HISTORYPARSERCONFIG_H
#define HISTORYPARSERCONFIG_H

class CHistoryData : public CLogParserData
{
	public:		
		CHistoryData(string strLogFile, string strInfoFile);
		virtual ~CHistoryData();

		string GetPathDatePatternHistory(string strDatetimeSuffix);

		string GetLastDatetime();
		void SetLastDatetime(string strDatetime);
		string GetNextDatetime(string strLastDatetime);

		string GetHistoryLogTail(string strProcessId);
		int GetHistoryLogPosition(string strProcessId);

		void* ReadFileBuffer(string, int&);
		int GetFileLength(string);
		
		void SetProcessPosition(string strProcessId, int iPosition);
};

#endif // HISTORYPARSERCONFIG_H