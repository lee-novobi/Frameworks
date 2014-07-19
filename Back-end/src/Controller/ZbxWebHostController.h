#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CZbxWebHostController:public CmysqlController
{
public:
	CZbxWebHostController(void);
	CZbxWebHostController(string strDBName);
	~CZbxWebHostController(void);
	
	bool FindDB();
	
	void ResetModel();
};

