#pragma once
#include "Action.h"
#include "../Common/Common.h"

class CCheckAction :
	public CAction
{
public:
	CCheckAction(void);
	~CCheckAction(void);

	MA_RESULT Do();
protected:
	CONDITION_ID	m_eConditionID;
};
