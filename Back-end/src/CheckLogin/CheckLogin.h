#include <curl/curl.h>
#include <curl/types.h>
#include <curl/easy.h>
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <vector>

using namespace std;

#define strHeader "Content-Type: application/x-www-form-urlencoded"
#define strCookiePath "cookielol.txt"
#define strUserAgent "Mozilla/5.0 Chromium/13.0.764.0 Chrome/13.0.764.0 Safari/534.35"
struct MemoryStruct {
  char *memory;
  size_t size;
};


class CCheckLogin
{
/*	protected:
		vector<string> m_v_strUrl;
		string m_strData;
		string m_strReferer;
		int m_iTimeOut;*/
		
	public:
		//CCheckLogin(vector<string>, string, string, int);
		CCheckLogin();
		~CCheckLogin();
		string PostPage(string, string, string, int);
		string GetPage(string, string, int);
		
		static size_t WriteCallback(void *contents, size_t size, size_t nmemb, void *userp){
			((string*)userp)->append((char*)contents, size * nmemb);
			return size * nmemb;
		}
};