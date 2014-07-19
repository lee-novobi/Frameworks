

// #ifndef LINUX
// #include "StdAfx.h"
// #endif
#include "DataLock.h"

CDataLock::CDataLock(void)
{
#ifdef LINUX
	//create mutex attribute variable
	pthread_mutexattr_t mAttr;

	// setup recursive mutex for mutex attribute
	pthread_mutexattr_settype(&mAttr, PTHREAD_MUTEX_RECURSIVE_NP);

	// Use the mutex attribute to create the mutex
	pthread_mutex_init(&m_sectionData, &mAttr);

	// Mutex attribute can be destroy after initializing the mutex variable
	pthread_mutexattr_destroy(&mAttr);
#endif
}

CDataLock::~CDataLock(void)
{
#ifdef LINUX
	pthread_mutex_destroy (&m_sectionData);
#endif
}

void CDataLock::Lock()
{
#ifndef LINUX
	m_sectionData.Lock();
#else
	pthread_mutex_lock(&m_sectionData);
#endif
	
	
}

void CDataLock::Unlock()
{
#ifndef LINUX
	m_sectionData.Unlock();
#else
	pthread_mutex_unlock(&m_sectionData);
#endif	
}
