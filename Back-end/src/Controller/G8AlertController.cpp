//#include "StdAfx.h"
#include "G8AlertController.h"
#include "../ExternalService/CurlService.h"
#include "../Common/DBCommon.h"

CG8AlertController::CG8AlertController(void)
{
	m_strTableName = ".g8_alerts";
}

CG8AlertController::~CG8AlertController(void)
{
}


bool CG8AlertController::UpdateAlertStatus(Query queryCondition, BSONObj bsonRecord)
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
			}
			catch(exception& ex)
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

bool CG8AlertController::UpdateG8Impact(Query queryCondition, BSONObj bsonRecord)
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
			}
			catch(exception& ex)
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

bool CG8AlertController::UpdateG8Reject(Query queryCondition)
{
	if(m_bIsConnected)
	{
		if(IsRecordExisted(queryCondition)){
			try{
				m_connDB.update(m_strTableName, queryCondition, 
				BSON("$set"<<BSON(ITSM_ID<<"")),false,true);
				return true;
			}
			catch(exception& ex)
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

bool CG8AlertController::UpdateStatusINC(string strLink, BSONObj bsonRecord, string strOutageEnd)
{
	string strAPIInfo, strResult, strSendInfo, strStatus;
	BSONObj bsonAPIInfo, bsonSendInfo;
	BSONArrayBuilder babTicketId;
	
	strStatus = CUtilities::RemoveBraces(bsonRecord[ITSM_STATUS].toString(false));
	if(strStatus.compare("closed") == 0)
		strStatus = "close";
	else if(strStatus.compare("resolved") == 0)
		strStatus = "resolve";	
	babTicketId << BSON(
						"\"code\""		<<CUtilities::RemoveBraces(bsonRecord[TICKET_ID].toString(false))<<
						"\"date\""		<<CUtilities::ReplaceString(CUtilities::GetCurrTime()," ","@")<<
						"\"status\""	<<CUtilities::ReplaceString(strStatus, "ed", "")
						);
	
	bsonSendInfo = BSON("send_info"<<babTicketId.arr());
	strSendInfo = CUtilities::ReplaceString(bsonSendInfo["send_info"].toString(false), " ", "");
	strSendInfo = CUtilities::ReplaceString(strSendInfo, "\"", "\\\"");
	bsonAPIInfo = BSON(
				"\"json_send_info\"" 	<< strSendInfo <<
				"\"function\"" 			<< "update_status_incident" <<
				"\"model\"" 			<< "vng.cstool.sdk.interface"
					);
	strAPIInfo = CUtilities::ReplaceString(bsonAPIInfo.toString(), " ", "");
	strAPIInfo = CUtilities::ReplaceString(strAPIInfo, "@", " ");
	
	strResult = CCurlService::CallLink(strLink, strAPIInfo);
	//================= hieutt test ====================
	cout << "strAPIInfo: " << strAPIInfo << endl;
	cout << "strResult: " << strResult << endl;
	//=================================================
	
	string strLog = CUtilities::FormatLog(LOG_MSG, "CG8AlertController", "G8_UpdateStatusINC",strResult);
	CUtilities::WriteErrorLog(strLog);
	
	if(strResult.find("\"1\":true") == std::string::npos)
		return false;
	return true;
}

bool CG8AlertController::ResetOperation(BSONObj bsonCondition)
{
	if(m_bIsConnected)
	{
		try{
			m_connDB.update(m_strTableName, QUERY(RECORD_ID<<OID(CUtilities::RemoveBraces(bsonCondition[SOURCE_ID].toString(false)))), 
			BSON("$set"<<BSON(OPERATION<<0)),false,true);
			return true;
		}
		catch(exception& ex)
		{
			string err = m_connDB.getLastError();
			stringstream strErrorMess;
			strErrorMess << ex.what() << ": " << err << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;	
}