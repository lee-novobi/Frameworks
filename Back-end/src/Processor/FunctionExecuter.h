#pragma once

#include "../Common/Common.h"
#include "CallBack.h"
#include "BaseThread.h"

class CDataLock;
class CWorkFlow;
class CSharedData;

class CFunctionExecuter: public CCallBack, public CBaseThread
{
public:
	CFunctionExecuter(void);
	~CFunctionExecuter(void);

	static CFunctionExecuter*	GetInstance();
	static void					ReleaseInstance();

	void			AddWorkFlow(CWorkFlow* pWorkFlow);
	void			OnAfterThreadExecute(CBaseThread* pThread);
	

protected:
	
	//void 			InitQueueWorkflow(WorkFlowQueue	m_queueWorkFlow);
	void			Destroy();
	CWorkFlow*		GetNextWorkFlow();
	MA_RESULT		ThreadExecute();
	unsigned int	GetRunningWorkFlowCount();
	
protected:
	static CFunctionExecuter*	s_pInstance;
	char HostPort[80];
	WorkFlowQueue	m_queueWorkFlow;
	unsigned int	m_nCurrentWorkFlowNumber;
	unsigned int	m_nCurrentWorkFlowIndex;
	CSharedData*	m_pSharedData;
	CDataLock*		m_lockThread;	
};
