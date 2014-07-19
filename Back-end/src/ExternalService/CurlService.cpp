#include "CurlService.h"
#include "../Common/ExternalCommon.h"

CCurlService::CCurlService(void)
{
}


CCurlService::~CCurlService(void)
{
}


string CCurlService::CallLink(string strLink, string strField)
{
	int iFind;
	CURL *curl;
	CURLcode res;
	string readBuffer, strLog;
	stringstream strErr;
	struct curl_slist *headers = NULL;
	headers = curl_slist_append(headers, HEADER);
	curl = curl_easy_init();
	curl_easy_setopt(curl, CURLOPT_URL, strLink.c_str());
    curl_easy_setopt(curl, CURLOPT_TIMEOUT, 3);
    curl_easy_setopt(curl, CURLOPT_USERAGENT, USER_AGENT);
    curl_easy_setopt(curl, CURLOPT_HEADER, 0);
	if(!strField.empty()) {
		curl_easy_setopt(curl, CURLOPT_POST, 1);
		curl_easy_setopt(curl, CURLOPT_POSTFIELDS, strField.c_str());
	}
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER,headers);
		
    res = curl_easy_perform(curl);
	if(res != CURLE_OK) {
		strErr << curl_easy_strerror(res);
		strLog = CUtilities::FormatLog(LOG_MSG, "CurlService", "CallLink","FAIL:" + strErr.str());
		CUtilities::WriteErrorLog(strLog);
	}
    curl_easy_cleanup(curl);
	
	return readBuffer;
}

