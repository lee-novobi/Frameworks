
// #include "PrototypeConfig.h"
// #ifndef LINUX
	//#include "StdAfx.h"
// #endif
#include "SharedData.h"

CSharedData* CSharedData::s_pInstance = NULL;
CSharedData::CSharedData(void)
{
}

CSharedData::~CSharedData(void)
{
}

CSharedData* CSharedData::GetInstance()
{
	if (CSharedData::s_pInstance == NULL)
	{
		CSharedData::s_pInstance = new CSharedData();
	}

	return CSharedData::s_pInstance;
}

void CSharedData::ReleaseInstance()
{
	if (NULL != s_pInstance)
	{
		delete s_pInstance;
		s_pInstance = NULL;
	}
}
