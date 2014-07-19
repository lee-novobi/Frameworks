#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CCheckCCUController :
	public CmysqlController
{
public:
	CCheckCCUController(void);
	~CCheckCCUController(void);
	void GetResult(CCUInfoQueue&);
protected:
};

