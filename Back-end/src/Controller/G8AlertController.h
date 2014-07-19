#pragma once
#include "MongodbController.h"

class CG8AlertController:public CMongodbController
{
public:
	CG8AlertController(void);
	~CG8AlertController(void);
	bool UpdateAlertStatus(Query queryCondition, BSONObj bsonRecord);
	bool UpdateG8Impact(Query queryCondition, BSONObj bsonRecord);
	bool UpdateG8Reject(Query queryCondition);
	bool UpdateStatusINC(string strLink, BSONObj bsonRecord, string strOutageEnd);
	bool ResetOperation(BSONObj bsonCondition);

};