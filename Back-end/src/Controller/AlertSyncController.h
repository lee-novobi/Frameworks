#pragma once
#include "MongodbController.h"

class CAlertSyncController:public CMongodbController
{
public:
	CAlertSyncController(void);
	~CAlertSyncController(void);
	
	bool InsertDB(BSONObj oCondition, BSONObj oRecord);
	bool UpdateDB(Query queryCondition, BSONObj bsonRecord);
	bool UpdateMaintenance(Query queryCondition, BSONObj bsonRecord);
	bool HideAlertNotInSrcId(string strSrcFrom, vector<string> vStrAlertId);
	bool RemoveAlertNotInTicketId(string strSrcFrom, vector<string> vStrAlertId);
	bool ChangeIsShowState(BSONObj oCondition);
	bool ChangeZbxIsShowState(BSONObj oCondition);
	bool HideAlertByVServerId(vector<long long> v_lServerId);
	bool HideAlertByVTriggerId(vector<long long> v_lTriggerId);
};