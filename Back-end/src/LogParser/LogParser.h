#pragma once

#include <sys/mman.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <stdlib.h>
#include <unistd.h>
#include <ctime>	
#include "../Common/Common.h"

class LogParser
{
	public:
		LogParser();
		~LogParser(void);
		int SkipSpecialChars(const char* chBuffer, int nCurPosition, int iLength);
		string GetToken(const char* chBuffer, int &nCurPosition, int iLength);
		string GetBlock(const char* chBuffer, int &nCurPosition, int iLength);
		string GetValueBlock(const char* chBuffer, int &nCurPosition, int iLength);
		string GetExpression(const char* chBuffer, int &nCurPosition, int iLength);
		string GetDescription(const char* chBuffer, int &nCurPosition, int iLength);
		string GetTriggerDescription(const char* chBuffer, int &nCurPosition, int iLength);
		string GetParameter(const char* chBuffer, int &nCurPosition, int iLength);
		string GetItemKey(const char* chBuffer, int &nCurPosition, int iLength);
		string GetItemValue(const char* chBuffer, int &nCurPosition, int iLength);
		void GoToNewLine(const char* chBuffer, int &nCurPosition, int iLength);
};