#pragma once
#include "../Common/Common.h"
#include "Config.h"
class CAlertProcessData: public CConfig
{
public:
	CAlertProcessData(void);
	CAlertProcessData(string strInfoFile);
	~CAlertProcessData(void);

	int GetPosition();
	void SetPosition(int iPosition);
};

