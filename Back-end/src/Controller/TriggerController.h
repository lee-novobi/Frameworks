#pragma once
#include "MongodbController.h"

class CTriggerController:public CMongodbController
{
public:
	CTriggerController(void);
	~CTriggerController(void);
	bool DeleteTriggerByVTriggerId(vector<long long> v_lTriggerId, int iLocation);
};
