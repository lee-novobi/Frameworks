#pragma once
#include "MongodbController.h"

class CAlertACKController:public CMongodbController
{
public:
	CAlertACKController(void);
	~CAlertACKController(void);
	bool CloneACK(string strNewObjId);

};