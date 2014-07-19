#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CSO6AlertController:public CmysqlController
{
public:
	CSO6AlertController (void);
	CSO6AlertController (string strDBName);
	~CSO6AlertController (void);
	bool SelectCritical();
};

