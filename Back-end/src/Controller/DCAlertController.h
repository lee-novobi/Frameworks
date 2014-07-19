#pragma once
#include "MongodbController.h"

class CDCAlertController:public CMongodbController
{
public:
	CDCAlertController(void);
	~CDCAlertController(void);
	bool UpdateAlertStatus(Query queryCondition, BSONObj bsonRecord);
	bool UpdateDCImpact(Query queryCondition, BSONObj bsonRecord);
	bool UpdateDCReject(Query queryCondition);
	bool UpdateStatusINC(string strLink, BSONObj bsonRecord, string strOutageEnd);
	bool ResetOperation(BSONObj bsonCondition);
};