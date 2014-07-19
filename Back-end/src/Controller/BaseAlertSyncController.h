#pragma once
#include "MongodbController.h"

class CAlertSyncController:public CMongodbController
{
public:
	CAlertSyncController(void);
	~CAlertSyncController(void);

	void Save();
};