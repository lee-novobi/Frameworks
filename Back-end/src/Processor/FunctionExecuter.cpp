

// #ifndef LINUX
// #include "StdAfx.h"
// #endif
#include "../Config/PrototypeConfig.h"
#include "FunctionExecuter.h"
#include "../Workflow/SharedData.h"
#include "../Workflow/WorkFlow.h"
#include "DataLock.h"
#include "../Config/DBParameters.h"
#include <ctime>
#include<unistd.h>
#define THREAD_POOL_NUM	10

CFunctionExecuter*	CFunctionExecuter::s_pInstance = NULL;

CFunctionExecuter*	CFunctionExecuter::GetInstance()
{
	if (s_pInstance == NULL)
	{
		s_pInstance = new CFunctionExecuter();
	}

	return s_pInstance;
}

void CFunctionExecuter::ReleaseInstance()
{
	if (s_pInstance != NULL)
	{
		delete s_pInstance;
		s_pInstance = NULL;
	}
}

CFunctionExecuter::CFunctionExecuter(void)
{
	strcpy(HostPort,HOST_NAME);
	strcat(HostPort,":");
	strcat(HostPort,paraPort);
	m_lockThread = new CDataLock();
	m_pSharedData = CSharedData::GetInstance();
	m_nCurrentWorkFlowNumber = 0;
	m_nCurrentWorkFlowIndex = 0;
	cout<<"CFunctionExecuter start"<<endl;
	Start();
}

CFunctionExecuter::~CFunctionExecuter(void)
{
	delete m_lockThread;
	CSharedData::ReleaseInstance();
	Destroy();
}

void CFunctionExecuter::Destroy()
{
	cout<<"Destroy"<<endl;
	// Destroy work flow objects
		/*for(int i = 0; i < m_queueWorkFlow.size(); i++)
		{
			cout<<"delete m_queueWorkFlow: "<<m_queueWorkFlow.size()<<endl;
			delete m_queueWorkFlow[i];
		}*/
	
	WorkFlowQueue::iterator it = m_queueWorkFlow.begin();
	while (it != m_queueWorkFlow.end())
	{
		cout<<"delete m_queueWorkFlow"<<endl;
		delete *it;
		it++;
		cout<<"Next for m_queueWorkFlow";
	}

	m_queueWorkFlow.clear();
}

void CFunctionExecuter::AddWorkFlow(CWorkFlow* pWorkFlow)
{
	cout<<"AddWorkFlow"<<endl;
	//m_lockThread->Lock();
	m_queueWorkFlow.push_back(pWorkFlow);
	//m_lockThread->Unlock();
}

/*void CFunctionExecuter::InitQueueWorkflow(WorkFlowQueue queueWorkFlow)
{
	
}*/

CWorkFlow*	CFunctionExecuter::GetNextWorkFlow()
{
	CWorkFlow* pWorkFlow = NULL;

	//m_lockThread->Lock();

	// Get next work flow from the queue
	/*if (m_queueWorkFlow.size() > 0)
	{
		cout<<"GetNextWorkFlow"<<endl;
		pWorkFlow = m_queueWorkFlow.front();
		m_queueWorkFlow.erase(m_queueWorkFlow.begin());
	}*/
	if(m_nCurrentWorkFlowIndex < m_queueWorkFlow.size())
		pWorkFlow = m_queueWorkFlow[m_nCurrentWorkFlowIndex++];
	//m_lockThread->Unlock();

	return pWorkFlow;
}

void CFunctionExecuter::OnAfterThreadExecute(CBaseThread* pThread)
{
	//m_lockThread->Lock();
	
	if (m_nCurrentWorkFlowNumber > 0)
	{
		//m_queueWorkFlow.erase(m_queueWorkFlow.begin() + m_nCurrentWorkFlowNumber - 1);
		m_nCurrentWorkFlowNumber--;
		cout<< "OnAfterThreadExecute : " << m_nCurrentWorkFlowNumber << endl;
	}
	//m_lockThread->Unlock();
	// Release thread
	delete pThread;

	
}

MA_RESULT CFunctionExecuter::ThreadExecute()
{
	cout<<"CFunctionExecuter::ThreadExecute()"<<endl;
	clock_t tstart = clock();
	double tcur;
	// cout << "ThreadExecute !!!"<<endl;
	MA_RESULT eResult = MA_RESULT_SUCCESS;
	#ifndef LINUX
		DWORD dwResult = 0;
	#endif
	
	CWorkFlow* pWorkFlow = NULL;
	while (true)
	{
	// cout << "LOOP !!!"<<endl;
	#ifndef LINUX
		dwResult = ::WaitForSingleObject(m_hEvent, m_nIdleTime);

		if (WAIT_TIMEOUT != dwResult)
		{
			break;
		}
	#else
		//tcur = (clock() - tstart)/(CLOCKS_PER_SEC/1000);
		//if (tcur >= m_nIdleTime)
		//{
			// cout<<tcur<<" | "<<m_nIdleTime<<endl;
		//	break;
		//}
		//cout << "sleep(20); !!!"<<endl;
	#endif
		if (GetRunningWorkFlowCount() < THREAD_POOL_NUM)
		{
			//cout << "GetRunningWorkFlowCount !!!"<<endl;
			// Get work flow to execute
			pWorkFlow = GetNextWorkFlow();
			if (pWorkFlow != NULL)
			{
				cout<<"pWorkFlow != NULL"<<endl;
				// Increase number of running thread
				//m_lockThread->Lock();
				m_nCurrentWorkFlowNumber++;				
				//m_lockThread->Unlock();

				// Execute workflow as thread
				pWorkFlow->SetCallBack(this);
				pWorkFlow->Start();
			}
		}
	}
	cout<<"end while !!"<<endl;
	return eResult;
}

unsigned int CFunctionExecuter::GetRunningWorkFlowCount()
{
	
	//cout<<"OUT "<<m_nCurrentWorkFlowNumber<<endl;
	//m_lockThread->Lock();
	//cout<<"IN"<<endl;
	unsigned int nCount = m_nCurrentWorkFlowNumber;
	//m_lockThread->Unlock();

	return nCount;
}