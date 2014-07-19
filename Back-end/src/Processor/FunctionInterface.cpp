
// #include "PrototypeConfig.h"
// #ifndef LINUX
	// #include "StdAfx.h"
// #endif
#include "FunctionInterface.h"
#include "../Action/Action.h"
#include "../Action/CheckPingAction.h"
#include "../Action/CheckTelnetAction.h"
CFunctionInterface*	CFunctionInterface::s_pInstance = NULL;
CFunctionInterface::CFunctionInterface(void)
{
}

CFunctionInterface::~CFunctionInterface(void)
{
}

CFunctionInterface*	CFunctionInterface::GetInstance()
{
	if (s_pInstance == NULL)
	{
		s_pInstance = new CFunctionInterface();
	}

	return s_pInstance;
}

void CFunctionInterface::ReleaseInstance()
{
	if (s_pInstance != NULL)
	{
		delete s_pInstance;
		s_pInstance = NULL;
	}
}

MA_RESULT CFunctionInterface::ExecuteFunction(METHOD_ID eMethodID)
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	switch (eMethodID)
	{
	case METHOD_MAKE_INC:
		cout<<"========================Action MakeInc();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case METHOD_CONTACT_SE:
		cout<<"========================Action ContactSE();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case METHOD_FOLLOW_KB:
		cout<<"========================Action FollowKB();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case METHOD_WAIT:
		cout<<"========================Action Wait();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case METHOD_FOLLOW:
		cout<<"========================Action Follow();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case METHOD_CONTACT_NOC:
		cout<<"========================Action ContactNOC();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	default:
		break;
	}

	return eResult;
}

MA_RESULT CFunctionInterface::CheckCondition(CONDITION_ID eCondition)
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	//CAction* pCheckAction;
	CCheckPingAction objPing;
	CCheckTelnetAction objTelnet;
	cout<<eCondition;
	switch (eCondition)
	{
	case CONDITION_SERVER_GAME:
		cout<<"========================Action CheckServer();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_LOGIN_GAME:
		cout<<"========================Action CheckLoginGame();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_TELNET:
		cout<<"========================Action CheckTelnet();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_SERVICE:
		cout<<"========================Action CheckService();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_PING:
		cout<<"========================Action CheckPing();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_LOGIN_WEB:
		cout<<"========================Action CheckLoginWeb();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_LOGIN_APP:
		cout<<"========================Action CheckLoginApp();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_CHECK_CCU:
		cout<<"========================Action CheckCCU();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	case CONDITION_SE_IMPACT:
		cout<<"========================Action CheckSEImpact();"<<endl;
		eResult = MA_RESULT_SUCCESS;
		break;
	default:
		break;
	}

	//delete pCheckAction;

	return eResult;
}

MA_RESULT CFunctionInterface::Ping()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	return eResult;
}
