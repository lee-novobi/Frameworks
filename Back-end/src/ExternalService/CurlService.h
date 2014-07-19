#pragma once
#include <curl/curl.h>
#include <curl/types.h>
#include <curl/easy.h>
#include "../Utilities/Utilities.h"
#include "../Common/Common.h"

#define HEADER "Content-Type: application/json"
#define COOKIE_PATH "cookielol.txt"
#define USER_AGENT "Mozilla/5.0 Chromium/13.0.764.0 Chrome/13.0.764.0 Safari/534.35"
struct MemoryStruct {
  char *memory;
  size_t size;
};


class CCurlService
{
public:
	CCurlService(void);
	~CCurlService(void);
	
	static string CallLink(string strLink, string strAPIInfo = "");

	static size_t WriteCallback(void *contents, size_t size, size_t nmemb, void *userp){
			((string*)userp)->append((char*)contents, size * nmemb);
			return size * nmemb;
	}
};

