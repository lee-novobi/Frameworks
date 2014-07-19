#pragma once
#include "../Common/Common.h"

class CSharedData
{
public:
	CSharedData(void);
	~CSharedData(void);

	static CSharedData* GetInstance();
	static void ReleaseInstance();
protected:
	static CSharedData* s_pInstance;
};
