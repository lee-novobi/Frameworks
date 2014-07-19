#pragma once
#include "MongodbController.h"

class CAlertController:public CMongodbController
{
public:
	bool UpdateSynced(string strSourceId);
	CAlertController(void);
	~CAlertController(void);

};

