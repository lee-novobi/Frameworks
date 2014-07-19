#include "./ExternalService/CurlService.h"
#include "Common/Common.h"
#include "Common/ExternalCommon.h"

int main(int argc, char* argv[])
{
	string strMonitorLink, strServiceLink, strResult;
	strMonitorLink = SDK_123P_WEB_MONITOR;
	strMonitorLink += argv[1];
	strServiceLink = CCurlService::CallLink(strMonitorLink);
	strResult = CCurlService::CallLink(strServiceLink);
	return 0;
}