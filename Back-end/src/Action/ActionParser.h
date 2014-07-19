#include <stdio.h>
#include <string.h>
#include <string>
#include <algorithm>
#include <iostream>

using namespace std;


class CActionParser
{
public:
	CActionParser();
	~CActionParser();
	virtual void DoAct() {};
	
	static string Get_token(const char* pData, int nDataLen, int& nPos)
	{
		string strToken = "";

		Skip_blank(pData, nDataLen, nPos);

		while (nPos < nDataLen)
		{
			if ((pData[nPos] == ' ') || (pData[nPos] == '\n'))
			{
				nPos++;
				break;
			}

		strToken += pData[nPos++];
		}

		return strToken;
	}
	
	static void Skip_blank(const char* pData, int nDataLen, int& nPos)
	{
		while (nPos < nDataLen)
		{
			if (pData[nPos] == ' ')
			{
				nPos++;
			}
			else
			{
				break;
			}
		}
	}
};



