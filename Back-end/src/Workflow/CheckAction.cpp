
// #include "PrototypeConfig.h"
// #ifndef LINUX
//	#include "StdAfx.h"
// #endif
#include "CheckAction.h"
#include "../Processor/FunctionInterface.h"

CCheckAction::CCheckAction(void)
{
}

CCheckAction::~CCheckAction(void)
{
}

MA_RESULT CCheckAction::Do()
{
	CFunctionInterface* pFunction = CFunctionInterface::GetInstance();

	return pFunction->CheckCondition(m_eConditionID);
}
