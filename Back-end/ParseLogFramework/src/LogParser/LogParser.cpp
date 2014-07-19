#include "LogParser.h"

LogParser::LogParser()
{
}

int LogParser::SkipSpecialChars(const char* chBuffer, int nCurPosition, int iLength)
{
	while (nCurPosition < iLength)
	{
		//cout << "SkipSpecialChars" << endl;
		if (chBuffer[nCurPosition] == ' ')
		{
			nCurPosition = nCurPosition + 1;
			continue;
		}

		else if (chBuffer[nCurPosition] == '\t')
		{
			nCurPosition = nCurPosition + 1;
			continue;
		}

		else if (chBuffer[nCurPosition] == '\n')
		{
			nCurPosition = nCurPosition + 1;
			continue;
		}
		break;
	}
	return nCurPosition;
}

string LogParser::GetValueBlock(const char* chBuffer, int &nCurPosition, int iLength)
{
	string strToken;
	nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);

	while(nCurPosition < iLength)
	{
		if (chBuffer[nCurPosition] == '{')
		{
			strToken = strToken + chBuffer[nCurPosition];
			nCurPosition += 1;
			continue;
		}
		else if (chBuffer[nCurPosition] == '\r')
		{
			nCurPosition += 1;
			continue;
		}
		else if (chBuffer[nCurPosition] == '}')
		{
			strToken = strToken + chBuffer[nCurPosition];
			nCurPosition += 1;
			if(chBuffer[nCurPosition] != ';')
				break;
		}
		if (chBuffer[nCurPosition] != '\n')
		{
			strToken = strToken + chBuffer[nCurPosition];
			//cout << strToken << endl;
		}
		nCurPosition = nCurPosition + 1;
	}
	return strToken;
}

string LogParser::GetBlock(const char* chBuffer, int &nCurPosition, int iLength)
{
	string strToken;
	nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
	bool bOpenBrace = false;
	while(nCurPosition < iLength)
	{
		if (chBuffer[nCurPosition] == '{')
		{
			strToken = strToken + chBuffer[nCurPosition];
			nCurPosition += 1;
			bOpenBrace = true;
			continue;
		}
		else if (chBuffer[nCurPosition] == '\r')
		{
			nCurPosition += 1;
			continue;
		}
		else if (chBuffer[nCurPosition] == '}')
		{
			strToken = strToken + chBuffer[nCurPosition];
			nCurPosition += 1;
			break;
		}
		if (chBuffer[nCurPosition] != '\n')
		{
			if(bOpenBrace == true)
				strToken = strToken + chBuffer[nCurPosition];
		}
		nCurPosition = nCurPosition + 1;
	}
	return strToken;
}

string LogParser::GetToken(const char* chBuffer, int &nCurPosition, int iLength)
{
	string strToken;
	bool bOpenBrace = false;
	nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
	while(nCurPosition < iLength)
	{
		if (chBuffer[nCurPosition] == '{')
		{
			bOpenBrace = true;
		}
		else if (chBuffer[nCurPosition] == ' ')
		{
			if (bOpenBrace == false)
			{
				nCurPosition = nCurPosition + 1;
				break;
			}
		}
		else if (chBuffer[nCurPosition] == '\n')
		{
			if (bOpenBrace == false)
			{
				nCurPosition = nCurPosition + 1;
				break;
			}
		}
		else if (chBuffer[nCurPosition] == '}')
		{
			strToken = strToken + chBuffer[nCurPosition];
			nCurPosition = nCurPosition + 1;
			break;
		}
		if (chBuffer[nCurPosition] != '\n')
		{
			strToken = strToken + chBuffer[nCurPosition];
			//cout << strToken << endl;
		}
		nCurPosition = nCurPosition + 1;
	}
	return strToken;
}

string LogParser::GetItemKey(const char* chBuffer, int &nCurPosition, int iLength)
{
	string strToken;
	bool bOpenBrace = false;
	nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
	while(nCurPosition < iLength)
	{
		if (chBuffer[nCurPosition] == '[')
		{
			bOpenBrace = true;
		}
		else if (chBuffer[nCurPosition] == ' ')
		{
			if (bOpenBrace == false)
			{
				nCurPosition = nCurPosition + 1;
				break;
			}
		}
		else if (chBuffer[nCurPosition] == '\n')
		{
			if (bOpenBrace == false)
			{
				nCurPosition = nCurPosition + 1;
				break;
			}
		}
		else if (chBuffer[nCurPosition] == ']')
		{
			strToken = strToken + chBuffer[nCurPosition];
			nCurPosition = nCurPosition + 1;
			break;
		}
		if (chBuffer[nCurPosition] != '\n')
		{
			strToken = strToken + chBuffer[nCurPosition];
			//cout << strToken << endl;
		}
		nCurPosition = nCurPosition + 1;
	}
	return strToken;
}

string LogParser::GetExpression(const char* chBuffer, int &nCurPosition, int iLength)
{
	string strExpression;
	nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
	bool bOpenBrace = false;
	while(nCurPosition < iLength)
	{
		if(chBuffer[nCurPosition] == ' ')
			//if(bOpenBrace == false)
		{
			nCurPosition = nCurPosition + 1;
			if(chBuffer[nCurPosition] == '&')
			{
				strExpression = strExpression + ' ';
				strExpression = strExpression + chBuffer[nCurPosition];
				nCurPosition = nCurPosition + 1;
				strExpression = strExpression + chBuffer[nCurPosition];
				nCurPosition = nCurPosition + 1;
				continue;
			}
			break;
		}
		else if(chBuffer[nCurPosition] == '\n')
			//if(bOpenBrace == false)
		{
			nCurPosition = nCurPosition + 1;
			break;
		}
		else if(chBuffer[nCurPosition] == '\r')
		{
			nCurPosition = nCurPosition + 1;
			continue;
		}

		if(chBuffer[nCurPosition] != '\n')
			strExpression = strExpression + chBuffer[nCurPosition];
			
		nCurPosition = nCurPosition + 1;
	}
	return strExpression;
}

string LogParser::GetDescription(const char* chBuffer, int &nCurPosition, int iLength)
{
		string strDescription;
		int iFlag1 = 0;
		int iFlag2 = 0;
		int left = 0;
		int right = 0;
		nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
		while(nCurPosition < iLength)
		{
			if(chBuffer[nCurPosition] == '"')
			{
				strDescription = strDescription + chBuffer[nCurPosition];
				nCurPosition = nCurPosition + 1;
				if(iFlag2 == 0)
				{
					iFlag1 = 1;
				}
				else if(iFlag2 == 1)
					break;
				continue;
			}
			else if(chBuffer[nCurPosition] == '{' && iFlag1 == 0)
			{
				if(left != 0)
					strDescription = strDescription + chBuffer[nCurPosition];
				nCurPosition = nCurPosition + 1;
				left++;
				continue;
			}
			else if(chBuffer[nCurPosition] == '}' && iFlag1 == 0)
			{
				right++;
				if(left != right)
				{
					strDescription = strDescription + chBuffer[nCurPosition];
					nCurPosition = nCurPosition + 1;
				}
				else
				{
					nCurPosition = nCurPosition + 1;
					break;
				}
				continue;
			}
			else if(chBuffer[nCurPosition] != '"' && iFlag1 == 1)
			{
				strDescription = strDescription + chBuffer[nCurPosition];
				nCurPosition = nCurPosition + 1;
				iFlag2 = 1;
				continue;
			}
			
			if (chBuffer[nCurPosition] != '\n')
				strDescription = strDescription + chBuffer[nCurPosition];

			nCurPosition = nCurPosition + 1;
		}
		nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);

		return strDescription;
}

string LogParser::GetParameter(const char* chBuffer, int &nCurPosition, int iLength)
{
		string strDescription;
		nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
		while(nCurPosition < iLength)
		{
			
			if(chBuffer[nCurPosition] == '{')
			{
				nCurPosition = nCurPosition + 1;
				continue;
			}
			else if(chBuffer[nCurPosition] == '}')
			{
				nCurPosition = nCurPosition + 1;
				break;
			}
			
			if (chBuffer[nCurPosition] != '\n')
				strDescription = strDescription + chBuffer[nCurPosition];

			nCurPosition = nCurPosition + 1;
		}
		//nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);

		return strDescription;
}

string LogParser::GetItemValue(const char* chBuffer, int &nCurPosition, int iLength)
{
	string strToken;
	nCurPosition = SkipSpecialChars(chBuffer, nCurPosition, iLength);
	while(nCurPosition < iLength)
	{
		if (chBuffer[nCurPosition] == '\n')
		{
			nCurPosition = nCurPosition + 1;
			break;
		}
		
		strToken = strToken + chBuffer[nCurPosition];
		nCurPosition = nCurPosition + 1;
	}
	return strToken;
}

LogParser::~LogParser(void)
{
}