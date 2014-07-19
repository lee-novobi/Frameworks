#pragma once
#include "MongodbController.h"

class CItemController:public CMongodbController
{
public:
	CItemController(void);
	~CItemController(void);
};
