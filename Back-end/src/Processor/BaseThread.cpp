// #include "StdAfx.h"
// #endif
#include "BaseThread.h"
#include "CallBack.h"

//#include <boost/thread.hpp>
#define THREAD_TERMINATION_TIMEOUT	2000
#define THREAD_IDLE_TIME			2

CBaseThread::CBaseThread(CCallBack* pCallBack)
{
#ifndef LINUX
	m_hEvent = CreateEvent(NULL, false, false, NULL);
#endif
	m_nIdleTime = THREAD_IDLE_TIME;
	m_pThread = NULL;
	m_pCallBack = pCallBack;
}

CBaseThread::~CBaseThread(void)
{
#ifndef LINUX
	// Force thread to stop
	Stop();
	
	DWORD dwResult = ::WaitForSingleObject(m_hEvent, THREAD_TERMINATION_TIMEOUT);

	// If thread is still running we need to terminate it
	if (WAIT_TIMEOUT == dwResult)
	{
		if(m_pThread->m_hThread)
		{
			DWORD dwExitCode = 0;
			GetExitCodeThread(m_pThread->m_hThread, &dwExitCode);
			TerminateThread(m_pThread->m_hThread, dwExitCode);
			CloseHandle(m_pThread->m_hThread);
			m_pThread = NULL;
		}	  					
	}

	CloseHandle (m_hEvent);
// #else
	// delete m_pThread;
#endif
}
#ifdef LINUX
void* 
#else
unsigned int
#endif
	CBaseThread::ThreadProc(void* lpFunctionObject)
{
	//m_BugLog << "ThreadProc"<<endl;
	CBaseThread* pThread = (CBaseThread*)lpFunctionObject;	

	pThread->ThreadExecute();
	//m_BugLog << "ThreadExecute end"<<endl;
	pThread->Stop();

	if (pThread->m_pCallBack != NULL)
	{
		pThread->m_pCallBack->OnAfterThreadExecute(pThread);
	}
	
	return 0;
}

void loopthread()
{
	while(true)
	{
		//cout << "loopthread"<<endl;
		//boost::this_thread::yield();
	}
}
void CBaseThread::Start()
{
#ifndef LINUX
	m_pThread = ::AfxBeginThread(CBaseThread::ThreadProc, this, THREAD_PRIORITY_NORMAL, 0, 0, NULL);
#else
	//boost::thread t(&loopthread);
	
	int create = pthread_create( &m_pThread, NULL, CBaseThread::ThreadProc, reinterpret_cast<void*>(this));
	if(create != 0) 
		//m_BugLog << "pthread_create's error"<<endl;
	//m_BugLog << "pthread_join"<<endl;
#endif
}

void CBaseThread::Stop()
{
#ifndef LINUX
	SetEvent(m_hEvent);
#endif
}
