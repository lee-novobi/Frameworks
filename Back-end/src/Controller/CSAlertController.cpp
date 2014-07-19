//#include "StdAfx.h"
#include "CSAlertController.h"
#include "../CSService/CSServiceReference.h"
#include "../Common/DBCommon.h"
// #include <QString>

CCSAlertController::CCSAlertController(void)
{
	m_strTableName = ".cs_alerts";
}

CCSAlertController::~CCSAlertController(void)
{
}

bool CCSAlertController::UpdateAlertStatus(Query queryCondition, BSONObj bsonRecord)
{
	if(m_bIsConnected)
	{
		if(IsRecordExisted(queryCondition)){
			try{
				m_connDB.update(m_strTableName, queryCondition, 
				BSON("$set"<<BSON(ITSM_STATUS<<CUtilities::RemoveBraces(bsonRecord[ITSM_STATUS].toString(false))
									<<ITSM_STATUS_NOTI<<bsonRecord[ITSM_STATUS_NOTI]._numberInt()
									<<STATUS<<bsonRecord[STATUS]._numberInt())),false,true);
				return true;
			}catch(exception& ex)
			{
				string err = m_connDB.getLastError();
				stringstream strErrorMess;
				strErrorMess << ex.what() << ": " << err << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
	}
	return false;
}

bool CCSAlertController::UpdateCSImpact(Query queryCondition, BSONObj bsonRecord)
{
	if(m_bIsConnected)
	{
		if(IsRecordExisted(queryCondition)){
			try{
				m_connDB.update(m_strTableName, queryCondition, 
				BSON("$set"<<BSON(	IMPACT_LEVEL<<bsonRecord[IMPACT_LEVEL]._numberInt()
									<<IMPACT_UPDATED_DATE_TIME<<CUtilities::RemoveBraces(bsonRecord[IMPACT_UPDATED_DATE_TIME].toString(false))
									<<IMPACT_UPDATED_UNIX<<bsonRecord[IMPACT_UPDATED_UNIX]._numberLong()
									<<ITSM_CASE<<bsonRecord[ITSM_CASE]._numberInt()
									<<SDK_ITSM_NOTI<<bsonRecord[SDK_ITSM_NOTI]._numberInt())),false,true);
				return true;
			}catch(exception& ex)
			{
				string err = m_connDB.getLastError();
				stringstream strErrorMess;
				strErrorMess << ex.what() << ": " << err << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
	}
	return false;
}

bool CCSAlertController::UpdateCSReject(Query queryCondition)
{
	if(m_bIsConnected)
	{
		if(IsRecordExisted(queryCondition)){
			try{
				m_connDB.update(m_strTableName, queryCondition, 
				BSON("$set"<<BSON(ITSM_ID<<"")),false,true);
				return true;
			}catch(exception& ex)
			{
				string err = m_connDB.getLastError();
				stringstream strErrorMess;
				strErrorMess << ex.what() << ": " << err << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
	}
	return false;
}

bool CCSAlertController::UpdateStatusINC(BSONObj bsonRecord, string strOutageEnd)
{
	string strResponse, strItsmStatus, strLog, strRejectMess;
	char *cINCCode, *cITSMCode, *cITSMCloseDate, *cCreatedBy, *cComment;
	short iINCStatusID = 99;
	short *p_iINCStatusID;
	strItsmStatus = CUtilities::RemoveBraces(bsonRecord[ITSM_STATUS].toString(false));
	strRejectMess = CUtilities::RemoveBraces(bsonRecord[MSG].toString(false));
	cINCCode = new char[100];
	cITSMCode = new char[100];
	cITSMCloseDate = new char[100];
	cCreatedBy = new char[100];
	cComment = new char[strRejectMess.size()+1];
	// =========Get Inc Status ===========
	if(strItsmStatus.compare("open") == 0)
		iINCStatusID = 24;
	else if(strItsmStatus.compare("closed") == 0)
		iINCStatusID = 26;
	else if(strItsmStatus.compare("reopen") == 0)
		iINCStatusID = 28;
	else if(strItsmStatus.compare("rejected") == 0)
		iINCStatusID = 27;
	else if(strItsmStatus.compare("resolved") == 0)
		iINCStatusID = 25;
	strcpy(cINCCode,CUtilities::RemoveBraces(bsonRecord[TICKET_ID].toString(false)).c_str());
	strcpy(cITSMCode,CUtilities::RemoveBraces(bsonRecord[ITSM_ID].toString(false)).c_str());	
	strcpy(cCreatedBy,"sdk");
	memcpy(cComment, strRejectMess.c_str(), strRejectMess.size() + 1);
	strcpy(cITSMCloseDate,strOutageEnd.c_str());
	p_iINCStatusID = &iINCStatusID;
	strResponse = CallUpdateStatusCSINC(cINCCode, p_iINCStatusID, cITSMCode, cCreatedBy, cComment, cITSMCloseDate);
	//============================Write Log==========================
	strLog = CUtilities::FormatLog(LOG_MSG, "UpdateAlertStt", "CSAlertController", "UpdateCSStatusINC:" + strResponse);
	CUtilities::WriteErrorLog(strLog);
	//============================Destroy==========================
	delete [] cINCCode;
	delete [] cITSMCode;
	delete [] cITSMCloseDate;
	delete [] cCreatedBy;
	delete [] cComment;
	
	if(strResponse == CODE_ERROR_INIT || strResponse == CODE_ERROR_UPDATE_STATUS_INC || strResponse == CODE_ERROR_SSL_CLIENT_CONTEXT || strResponse.find(RESPONSE_UNSUCCESS) != std::string::npos || strResponse.find("fail") != std::string::npos)
	{
		return false;
	}
	return true;
}
