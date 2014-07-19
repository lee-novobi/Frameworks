#include "ChangesController.h"
#include "../Common/DBCommon.h"
#include "../Model/ChangesModel.h"

CChangesController::CChangesController(void)
{
	m_strTableName = ".changes";
}
CChangesController::~CChangesController(void)
{
}
void CChangesController::Init()
{
}
void CChangesController::Destroy()
{
}

char* CChangesController::GetActiveCollection(const char *pCharCollectionName)
{
	char* pResult;
	pResult = NULL;

	CChangesModel objChangesModel;

	try
	{
		Query queryChanges = objChangesModel.GetCollectionByName(pCharCollectionName);
		
		if(FindDB(queryChanges))
		{
			NextRecord();
			pResult = (char*)GetFieldString("active").c_str();		
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strFormatLog;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CChangesController", "GetActiveCollection", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	
	return pResult;
}

char* CChangesController::GetPassiveCollection(const char *pCharCollectionName)
{
	char* pResult;
	pResult = NULL;

	CChangesModel objChangesModel;

	try
	{
		Query queryChanges = objChangesModel.GetCollectionByName(pCharCollectionName);
		
		if(FindDB(queryChanges))
		{
			NextRecord();
			pResult = (char*)GetStringResultVal("passive").c_str();		
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strFormatLog;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CChangesController", "GetPassiveCollection", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	return pResult;
}

void CChangesController::SwitchActiveCollection(const char* pCharCollectionName)
{
	CChangesModel objChangesModel;
	string strName;
	string strActiveCollection;
	string strPassiveCollection;
	BSONObj bsonSet;

	try
	{
		Query queryChanges = objChangesModel.GetCollectionByName(pCharCollectionName);

		if(FindDB(queryChanges))
		{
			NextRecord();
			strActiveCollection = GetStringResultVal("active");
			strPassiveCollection = GetStringResultVal("passive");
			bsonSet = BSON("active" << strPassiveCollection << "passive" << strActiveCollection);

			UpdateDB(queryChanges, bsonSet);
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strFormatLog;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CChangesController", "SwitchActiveCollection", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
}