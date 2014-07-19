#include "CSAlertSyncProcess.h"
#include "../Controller/CSAlertController.h"
#include "../Controller/AlertSyncController.h"
#include "../Controller/ImpactLevelController.h"
#include "../Controller/MapProductController.h"

#include "../Model/MongodbModel.h"
#include "../Model/AlertSyncModel.h"
#include "../Model/ImpactLevelModel.h"
#include "../Model/MapProductModel.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CCSAlertSyncProcess::CCSAlertSyncProcess(void)
{
	
}

CCSAlertSyncProcess::CCSAlertSyncProcess(string strCfgFile)
{
	m_pSourceController = new CCSAlertController();
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	//======Read Config File======//
	m_strInfo = m_pConfigFile->ReadStringValue(CS_ALERT_SYNC_GROUP,INFOPATH);
	//=============================//
	Init();
}

CCSAlertSyncProcess::~CCSAlertSyncProcess(void)
{
	Destroy();
}

int CCSAlertSyncProcess::CreateModel()
{
	// int iNumOfCase, iAlertNumOfCase, iImpactLevel;
	// string strDescription, strSourceId, strTitle, strProduct, strAttachment, strErrorMsg, strServerName;
	// iImpactLevel = NULL;
	// try{
// ========================================Get Alert Data==============================================
		// iAlertNumOfCase = atoi(m_pConfigFile->GetData(CS_ALERT_SYNC_GROUP,ALERT_NUM_OF_CASE).c_str());
		// iNumOfCase =  m_pSourceController->GetIntResultVal(NUM_OF_CASE);
		// if(iNumOfCase < iAlertNumOfCase)
			// return 0;
		// strErrorMsg =  CUtilities::RemoveBraces(m_pSourceController->GetStringResultVal(CS_ERROR_MSG));
		// strServerName =  CUtilities::RemoveBraces(m_pSourceController->GetStringResultVal(CS_SERVER_NAME));
		// strDescription = CUtilities::RemoveBraces(m_pSourceController->GetStringResultVal(DESCRIPTION)) + "_" + strErrorMsg + "_" + strServerName;
		// strSourceId =  CUtilities::GetMongoObjId(m_pSourceController->GetStringResultVal(RECORD_ID));
		// strTitle =  CUtilities::RemoveBraces(m_pSourceController->GetStringResultVal(TITLE));
		// strProduct =  CUtilities::RemoveBraces(m_pSourceController->GetStringResultVal(PRODUCT));
		// strAttachment =  m_pSourceController->GetStringResultVal(ATTACHMENTS);
		
		// ====================Get Impact Level==================
		// m_pImpactLevelModel->SetRecordBson(SOURCE_FROM,CS_SOURCE_FROM_VAL);
		// m_pImpactLevelModel->SetRecordBson(NUM_OF_CASE,iNumOfCase);
		// if(m_pImpactLevelController->FindDB(m_pImpactLevelModel->GetImpactLevelByCaseNumQuery()))
			// iImpactLevel = m_pImpactLevelController->GetIntResultVal(IMPACT_LEVEL);
		
		// =====================Get ITSM Product===========================
		// m_pMapProductModel->SetRecordBson(MAP_SOURCE,CS_SOURCE_FROM_VAL);
		// m_pMapProductModel->SetRecordBson(MAP_SRC_PRODUCT,strProduct);
		// if(m_pMapProductController->FindDB(m_pMapProductModel->GetMapProductBySrcProductQuery()))
			// strProduct = CUtilities::RemoveBraces(m_pImpactLevelController->GetStringResultVal(MAP_ITSM_PRODUCT));
// ================================Append Model===========================================
		// m_pAlertSyncModel->SetRecordBson(IS_SHOW,1);
		// m_pAlertSyncModel->SetRecordBson(INTERNAL_STATUS);
		// m_pAlertSyncModel->SetRecordBson(EXTERNAL_STATUS);
		// m_pAlertSyncModel->SetRecordBson(SOURCE_FROM,CS_SOURCE_FROM_VAL);
		// m_pAlertSyncModel->SetRecordBson(SOURCE_ID,strSourceId);
		// m_pAlertSyncModel->SetRecordBson(TITLE,strTitle);
		// m_pAlertSyncModel->SetRecordBson(DESCRIPTION,strDescription);
		// m_pAlertSyncModel->SetRecordBson(DEPARTMENT, CS_SOURCE_FROM_VAL);
		// m_pAlertSyncModel->SetRecordBson(PRODUCT,strProduct);
		// m_pAlertSyncModel->SetRecordBson(ATTACHMENTS,strAttachment);
		// m_pAlertSyncModel->SetRecordBson(NUM_OF_CASE,iNumOfCase);
		// m_pAlertSyncModel->SetRecordBson(IMPACT_LEVEL,iImpactLevel);
		// m_pAlertSyncModel->SetRecordBson(IS_ACK);
		// m_pAlertSyncModel->SetRecordBson(ACK_MSG);
		// m_pAlertSyncModel->SetRecordBson(ALERT_MSG,strDescription);
		// m_pAlertSyncModel->SetRecordBson(ITSM_INC_ID);
		// m_pAlertSyncModel->SetRecordBson(CREATE_DATE,CUtilities::GetCurrTimeStamp());
		// m_pAlertSyncModel->SetRecordBson(UPDATE_DATE);
	// }
	// catch(exception &ex)
	// {	
		// stringstream strErrorMess;
		// strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
		// CUtilities::WriteErrorLog(strErrorMess.str());
	// }
	// return 1;
}