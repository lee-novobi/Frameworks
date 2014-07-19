#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CWorkFlowController :
	public CmysqlController
{
public:
	CWorkFlowController(void);
	~CWorkFlowController(void);
	void GetResult(ActInfoQueue&);
};

