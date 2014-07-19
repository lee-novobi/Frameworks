#include "CheckLogin.h"

CCheckLogin::CCheckLogin()
{
}
/*
CCheckLogin::CCheckLogin(vector<string> v_strUrl, string strData, string strReferer, int iTimeOut)
{
	for(int i = 0; i < v_strUrl.size(); i++)
		m_v_strUrl.push_back(v_strUrl[i]);
	m_strData = strData;
	m_strReferer = strReferer;
	m_iTimeOut = iTimeOut;
}
*/
CCheckLogin::~CCheckLogin()
{
}

string CCheckLogin::PostPage(string str_Url, string strData, string strReferer, int iTimeOut)
{
	CURL *curl;
	CURLcode res;
	string readBuffer;
	struct curl_slist *headers = NULL;
	//headers = curl_slist_append(headers, "Accept: application/json");
	headers = curl_slist_append(headers, strHeader);
	//"Content-Type: application/x-www-form-urlencoded");
	//headers = curl_slist_append(headers, "charsets: utf-8");
	if(iTimeOut == 0)
        iTimeOut=30;
    curl = curl_easy_init();
    if(strReferer.compare("") != 0){
        curl_easy_setopt (curl, CURLOPT_REFERER, strReferer.c_str());
    }
    curl_easy_setopt (curl, CURLOPT_URL, str_Url.c_str());
    curl_easy_setopt (curl, CURLOPT_COOKIEJAR, strCookiePath);
    curl_easy_setopt (curl, CURLOPT_TIMEOUT, iTimeOut);
    curl_easy_setopt (curl, CURLOPT_USERAGENT, strUserAgent);
    curl_easy_setopt (curl, CURLOPT_HEADER, true);
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    curl_easy_setopt (curl, CURLOPT_SSL_VERIFYPEER, false);
    //curl_easy_setopt (curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_easy_setopt (curl, CURLOPT_POST, 1);
    curl_easy_setopt (curl, CURLOPT_POSTFIELDS, strData.c_str());
	//"u=hieutt1907&p=IMshark7&x=45&y=28&u1=http://idgunny.zing.vn/index/server&pid=38&fp=http://idgunny.zing.vn/index/");
    curl_easy_setopt (curl, CURLOPT_HTTPHEADER,headers);
	
    res = curl_easy_perform (curl);
	//echo $html;
	if(res != CURLE_OK) {
		fprintf(stderr, "curl_easy_perform() failed: %s\n",
				curl_easy_strerror(res));
	}
	//cout<<readBuffer<<endl;
    curl_easy_cleanup(curl);
    return readBuffer;
}
		
string CCheckLogin::GetPage(string str_Url, string strReferer, int iTimeOut)
{
	CURL *curl;
	CURLcode res;
	string readBuffer;
	if(iTimeOut == 0)
        iTimeOut=30;
    curl = curl_easy_init();
    if(strReferer.compare("") != 0){
        curl_easy_setopt (curl, CURLOPT_REFERER, strReferer.c_str());
    }
    curl_easy_setopt (curl, CURLOPT_URL, str_Url.c_str());
    curl_easy_setopt (curl, CURLOPT_COOKIEFILE, strCookiePath);
    curl_easy_setopt (curl, CURLOPT_TIMEOUT, iTimeOut);
    curl_easy_setopt (curl, CURLOPT_USERAGENT, strUserAgent);
	//"Mozilla/5.0 (X11; Linux i686) AppleWebKit/534.35 (KHTML, like Gecko) Ubuntu/10.10 Chromium/13.0.764.0 Chrome/13.0.764.0 Safari/534.35 CentOS/6.2 Apache/2.2.15");
    curl_easy_setopt (curl, CURLOPT_HEADER, false);
    //curl_easy_setopt (curl, CURLOPT_RETURNTRANSFER, 1);
    curl_easy_setopt (curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    res = curl_easy_perform (curl);
	//echo $html;
	if(res != CURLE_OK) {
		fprintf(stderr, "curl_easy_perform() failed: %s\n",
				curl_easy_strerror(res));
	}
	//cout<<readBuffer<<endl;
    curl_easy_cleanup(curl);
    return readBuffer;
}