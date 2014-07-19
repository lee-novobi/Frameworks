#pragma once
#include "MongodbController.h"

class CCSAlertController:public CMongodbController
{
public:
	CCSAlertController(void);
	~CCSAlertController(void);

	void Save();
	int FindCount();
	bool UpdateAlertStatus(Query queryCondition, BSONObj bsonRecord);
	bool UpdateCSImpact(Query queryCondition, BSONObj bsonRecord);
	bool UpdateCSReject(Query queryCondition);
	bool UpdateStatusINC(BSONObj bsonRecord, string strOutageEnd);
};