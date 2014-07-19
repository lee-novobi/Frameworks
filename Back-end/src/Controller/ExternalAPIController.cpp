#include "ExternalAPIController.h"


CExternalAPIController::CExternalAPIController(void)
{
}


CExternalAPIController::~CExternalAPIController(void)
{
}


string CExternalAPIController::SNS_CollectVIDInfo(string strMacAddr)
{
	int iFind;
	CURL *curl;
	CURLcode res;
	string readBuffer, strData;
	string strVIMkey;
	map<string,string> VIM_INFO;
	struct curl_slist *headers = NULL;
	headers = curl_slist_append(headers, strHeader);
	CUtilities::ReplaceString(strMacAddr,"-",":");
	strData = "http://icloud.vng.com.vn/service/getVMkey/?mac=" + strMacAddr;

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
	strVIMkey = CUtilities::VIMJsonParser(readBuffer);
	return strVIMkey;
}
