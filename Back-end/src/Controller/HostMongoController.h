#pragma once
#include "MongodbController.h"

class CHostMongoController:public CMongodbController
{
public:
	CHostMongoController(void);
	~CHostMongoController(void);
};