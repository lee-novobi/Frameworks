#include "123PWebMonitorService.h"
#include "../Common/ExternalCommon.h"

C123PWebMonitorService::C123PWebMonitorService(void)
{
}


C123PWebMonitorService::~C123PWebMonitorService(void)
{
}


string C123PWebMonitorService::API123PWebMonitor(string strPara)
{
	int iFind;
	CURL *curl;
	CURLcode res;
	string readBuffer, strData;
	string strVIMkey;
	map<string,string> VIM_INFO;
	struct curl_slist *headers = NULL;
	headers = curl_slist_append(headers, strHeader);
	strData = SDK_123P_WEB_MONITOR;
	strData += strPara;
	curl = curl_easy_init();
	curl_easy_setopt (curl, CURLOPT_URL, strData.c_str());
    curl_easy_setopt (curl, CURLOPT_TIMEOUT, 3);
    curl_easy_setopt (curl, CURLOPT_USERAGENT, strUserAgent);
    curl_easy_setopt (curl, CURLOPT_HEADER, true);
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    curl_easy_setopt (curl, CURLOPT_HTTPHEADER,headers);
		
    res = curl_easy_perform (curl);
	//echo $html;
	if(res != CURLE_OK) {
		fprintf(stderr, "curl_easy_perform() failed: %s\n",
				curl_easy_strerror(res));
	}
    curl_easy_cleanup(curl);
	
	iFind = readBuffer.find_last_of("\n");
	readBuffer = readBuffer.substr(iFind+1);
	// strVIMkey = CUtilities::VIMJsonParser(readBuffer);
	cout << readBuffer << endl;
	CallServiceLink(readBuffer);
	return readBuffer;
}


string C123PWebMonitorService::CallServiceLink(string strPara)
{
	int iFind;
	CURL *curl;
	CURLcode res;
	string readBuffer, strData;
	string strVIMkey;
	map<string,string> VIM_INFO;
	struct curl_slist *headers = NULL;
	headers = curl_slist_append(headers, strHeader);
	strData = strPara;
	curl = curl_easy_init();
	curl_easy_setopt (curl, CURLOPT_URL, strData.c_str());
    curl_easy_setopt (curl, CURLOPT_TIMEOUT, 3);
    curl_easy_setopt (curl, CURLOPT_USERAGENT, strUserAgent);
    curl_easy_setopt (curl, CURLOPT_HEADER, true);
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    curl_easy_setopt (curl, CURLOPT_HTTPHEADER,headers);
		
    res = curl_easy_perform (curl);
	//echo $html;
	if(res != CURLE_OK) {
		fprintf(stderr, "curl_easy_perform() failed: %s\n",
				curl_easy_strerror(res));
	}
    curl_easy_cleanup(curl);
	
	iFind = readBuffer.find_last_of("\n");
	readBuffer = readBuffer.substr(iFind+1);
	// strVIMkey = CUtilities::VIMJsonParser(readBuffer);
	cout << readBuffer << endl;
	
	return readBuffer;
}

