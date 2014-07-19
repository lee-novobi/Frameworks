#pragma once
#include <curl/curl.h>
#include <curl/types.h>
#include <curl/easy.h>
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <vector>
#include <map>
#include "../Utilities/Utilities.h"

using namespace std;

#define strHeader "Content-Type: application/x-www-form-urlencoded"
#define strCookiePath "cookielol.txt"
#define strUserAgent "Mozilla/5.0 Chromium/13.0.764.0 Chrome/13.0.764.0 Safari/534.35"
struct MemoryStruct {
  char *memory;
  size_t size;
};


class CExternalAPIController
{
public:
	CExternalAPIController(void);
	~CExternalAPIController(void);
	
	static string SNS_CollectVIDInfo(string);

	static size_t WriteCallback(void *contents, size_t size, size_t nmemb, void *userp){
			((string*)userp)->append((char*)contents, size * nmemb);
			return size * nmemb;
	}
};

