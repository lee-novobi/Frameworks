#pragma once
#include "../Common/Common.h"
#include "../Config/PrototypeConfig.h"
#ifdef LINUX
extern "C"
{
   #include <pthread.h>
}
#endif
using namespace std;
class CCallBack;

class CBaseThread
{
public:
	CBaseThread(CCallBack* pCallBack = NULL);
	~CBaseThread(void);

	void Start();
	void Stop();

	inline void SetCallBack(CCallBack* pCallBack) { m_pCallBack = pCallBack; }
	inline MA_RESULT GetResult() { return m_eResult; }

protected:
	virtual MA_RESULT ThreadExecute() = 0;

protected:
	static
#ifdef LINUX
void* 
#else
unsigned int
#endif
		ThreadProc(void* lpFunctionObject);

protected:

#ifdef LINUX
	pthread_t		m_pThread;
#else
	CWinThread*		m_pThread;
	HANDLE			m_hEvent;
#endif
	unsigned int	m_nIdleTime;
	CCallBack*		m_pCallBack;
	MA_RESULT		m_eResult;
	ofstream m_BugLog;
};
