//#include "StdAfx.h"
#include "HostMongoController.h"
#include "../ExternalService/CurlService.h"
#include "../Common/DBCommon.h"

CHostMongoController::CHostMongoController(void)
{
	m_strTableName = ".hosts";
}

CHostMongoController::~CHostMongoController(void)
{
}
