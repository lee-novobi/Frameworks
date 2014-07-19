#pragma once
#include "../Common/Common.h"
#include "Config.h"

class CSyncProcessData: public CConfig
{
public:
	CSyncProcessData(void);
	CSyncProcessData(string strInfoFile);
	~CSyncProcessData(void);

	int GetPosition();
	void SetPosition(int iPosition);
};