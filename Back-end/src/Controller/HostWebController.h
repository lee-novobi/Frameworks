#pragma once
#include "MongodbController.h"

class CHostWebController:public CMongodbController
{
public:
	CHostWebController(void);
	~CHostWebController(void);
	
	bool UpdateDB(Query queryCondition, BSONObj bsonRecord);
	bool UpdateDelete(Query queryCondition, BSONObj bsonRecord);
};