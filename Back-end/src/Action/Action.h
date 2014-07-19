#pragma once
#include "../Common/Common.h"

class CAction
{
public:
	CAction(void);
	~CAction(void);

	virtual MA_RESULT Do() { return MA_RESULT_UNKNOWN; }

protected:
	METHOD_ID m_eMethodID;
};
