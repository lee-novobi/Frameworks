#include "CheckCCUController.h"
#define ClockIndex 1
#define CCUIndex 2

CCheckCCUController::CCheckCCUController(void)
{
}


CCheckCCUController::~CCheckCCUController(void)
{
}

void CCheckCCUController::GetResult(CCUInfoQueue& queueCCUInfo)
{
	MYSQL_ROW row;
	int iCCU,iClock;
	CCU_INFO CCUInfo;
	for ( int i = 0; i < m_result->row_count; ++i )
    {
        row = mysql_fetch_row(m_result);
        // In tat ca cac colume:
        //for ( int col = 0; col < mysql_num_fields(result); ++col )
		//{
		
		iClock = atoi(row[ClockIndex]);
		iCCU = atoi(row[CCUIndex]);
		CCUInfo.m_iCCU = iCCU;
		CCUInfo.m_iClock = iClock;
		queueCCUInfo.push_back(CCUInfo);
    }
}