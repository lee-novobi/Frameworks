#pragma once
#include "MongodbController.h"

class CChangesController:
	public CMongodbController
{
public:
	CChangesController(void);
	~CChangesController(void);
	
	void Init();
	void Destroy();
	char* GetActiveCollection(const char* pCharCollectionName);
	char* GetPassiveCollection(const char* pCharCollectionName);
	void SwitchActiveCollection(const char* pCharCollectionName);
};
