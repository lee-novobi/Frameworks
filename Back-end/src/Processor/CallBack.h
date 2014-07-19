#pragma once

class CBaseThread;
class CCallBack
{
public:
	CCallBack(void);
	~CCallBack(void);

	virtual void OnAfterThreadExecute(CBaseThread* pThread) = 0;
};
