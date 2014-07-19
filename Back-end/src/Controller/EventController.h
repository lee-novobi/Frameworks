#pragma once
#include "MongodbController.h"

class CEventController:public CMongodbController
{
public:
	bool UpdateSynced(string strSourceId);
	CEventController(void);
	~CEventController(void);
};
