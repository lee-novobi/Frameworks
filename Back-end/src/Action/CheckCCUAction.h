#include "Action.h"
#include "../Common/Common.h"

typedef struct TimeStamp
{
	string m_strStartTimeStamp;
	string m_strEndTimeStamp;
} TIMESTAMP;



class CCheckCCUAction:public CAction
{
public:
	CCheckCCUAction();
	CCheckCCUAction(string strAlertTime, string strServerKey, int nMonBack);
	CCheckCCUAction(int Y, int M, int D, int H, int Mi, int S, string strServerKey, int nMonBack);
	~CCheckCCUAction();
	MA_RESULT Do();
protected:
	TIMESTAMP GetTimeStamp(int Y, int M, int D, int H, int Mi, int S, int nMonBack);
	void LoadCCUInfo();
	int m_iHighestCCU;
	int m_iPrevCCU;
	TIMESTAMP m_tsCheckCCUTime;
	string m_strServerKey;
	vector<int> m_queueDownPer;
	vector<int> m_queueDownPerPrev;
	CCUInfoQueue m_queueCCUInfo;
	
};