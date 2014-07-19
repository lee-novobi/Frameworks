#include "WorkFlowController.h"
#define FlowIndex 0
#define ActIndex 1
#define ResultIndex 2
#define NextFlowIndex 3
#define MethodIdIndex 5
#define ConditionIdIndex 6

CWorkFlowController::CWorkFlowController()
{

}


CWorkFlowController::~CWorkFlowController()
{
}


void CWorkFlowController::GetResult(ActInfoQueue& queueActInfo)
{
	MYSQL_ROW row;
	int iFlowId, iActId, iResult, iNextFlowId, iMethodID, iConditionID;
	ACT_INFO ActInfo;
	cout<<"result->row_count :"<<result->row_count<<endl;
	for ( int i = 0; i < result->row_count ; i++ )
    {
        row = mysql_fetch_row(result);
        // In tat ca cac colume:
        //for ( int col = 0; col < mysql_num_fields(result); ++col )
		//{
		
		iFlowId = atoi(row[FlowIndex]);
		iActId = atoi(row[ActIndex]);
		iResult = atoi(row[ResultIndex]);
		iNextFlowId = atoi(row[NextFlowIndex]);
		iMethodID = atoi(row[MethodIdIndex]);
		iConditionID = atoi(row[ConditionIdIndex]);
		
		ActInfo.m_iFlowId = iFlowId;
		ActInfo.m_iActId = iActId;
		ActInfo.m_iNextFlowId = iNextFlowId;
		ActInfo.m_eResult = (MA_RESULT)iResult;
		ActInfo.m_eMethodID = (METHOD_ID)iMethodID;
		ActInfo.m_eConditionID = (CONDITION_ID)iConditionID;

		queueActInfo.push_back(ActInfo);
    }
}