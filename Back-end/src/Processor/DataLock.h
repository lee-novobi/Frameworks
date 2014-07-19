#pragma once

#include "../Config/PrototypeConfig.h"

#ifdef LINUX
extern "C"
{
   #include <pthread.h>
}
#endif


class CDataLock
{
public:
	CDataLock(void);
	~CDataLock(void);

	void Lock();
	void Unlock();

protected:
#ifndef LINUX
	CCriticalSection m_sectionData;
#else
	pthread_mutex_t m_sectionData;
#endif
};
