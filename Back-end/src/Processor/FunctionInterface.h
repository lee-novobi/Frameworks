#pragma once
#include "../Common/Common.h"


class CFunctionInterface
{
public:
	CFunctionInterface(void);
	~CFunctionInterface(void);

	static CFunctionInterface*	GetInstance();
	static void					ReleaseInstance();

	MA_RESULT					ExecuteFunction(METHOD_ID eMethodID);
	MA_RESULT					CheckCondition(CONDITION_ID eCondition);

protected:
	MA_RESULT					Ping();
protected:
	static CFunctionInterface*	s_pInstance;
};
