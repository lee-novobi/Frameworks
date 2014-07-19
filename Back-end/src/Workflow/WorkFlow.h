#pragma once
//#include "StdAfx.h"
#include "../Processor/BaseThread.h"
#include "../Common/Common.h"
class CNode;
class CWorkFlowData;

typedef vector<CNode*> NodeArray;

class CWorkFlow : public CBaseThread
{
public:
	CWorkFlow(void);
	CWorkFlow(string strDescription);
	~CWorkFlow(void);

	void OnAfterThreadExecute(CBaseThread* pThread);
	MA_RESULT	Execute();
	void Insert(int iFlagBack,  int iActId, MA_RESULT eResult, int iChildArraySize, METHOD_ID eMethodID, CONDITION_ID eConditionID);
	void Reset();
	inline string GetDescription() {return m_strDescription;}
	
protected:
	CNode*		GetActivatedNode();
	CNode*		GetActivatedNode(MA_RESULT eResult);
	MA_RESULT	ThreadExecute();

protected:
	int				m_iFlagBack;
	CNode*			m_pRoot;
	CNode*			m_pCurrentNode;
	//CWorkFlowData*	m_pData;
	string			m_strDescription;
};
