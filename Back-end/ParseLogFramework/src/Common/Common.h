#pragma once

#include "../Utilities/Utilities.h"
#include <vector>
#include <queue>
#include <stdlib.h>
#include <stdio.h>
#include <algorithm>
#include <string.h>
#include <iostream>
#include <fstream>
#include <string>
#include <sstream>
#include <time.h>
#include <map>
#include <iterator> // for ostream_iterator
using namespace std;

class CWorkFlow;
typedef vector<CWorkFlow*> WorkFlowQueue;

typedef enum
{
	MA_RESULT_FAIL = 0,
	MA_RESULT_SUCCESS = 1,
	MA_RESULT_UNKNOWN = 2
} MA_RESULT;

typedef enum
{
	METHOD_MAKE_INC = 0,
	METHOD_CONTACT_SE = 1,
	METHOD_FOLLOW_KB = 2,
	METHOD_WAIT = 3,
	METHOD_FOLLOW = 4,
	METHOD_CONTACT_NOC = 5
} METHOD_ID;

typedef enum
{
	CONDITION_SERVER_GAME = 0,
	CONDITION_LOGIN_GAME = 1,
	CONDITION_TELNET = 2,
	CONDITION_SERVICE = 3,
	CONDITION_PING = 4,
	CONDITION_LOGIN_WEB = 5,
	CONDITION_LOGIN_APP = 6,
	CONDITION_CHECK_CCU = 7,
	CONDITION_SE_IMPACT = 8
} CONDITION_ID;

typedef struct CCU_Info
{
	int m_iCCU;
	int m_iClock;
} CCU_INFO;
typedef vector<CCU_INFO> CCUInfoQueue;

typedef struct Act_Info
{
	int m_iFlowId;
	int m_iActId;
	int m_iNextFlowId;
	MA_RESULT m_eResult;
	METHOD_ID m_eMethodID;
	CONDITION_ID m_eConditionID;
} ACT_INFO;
typedef vector<ACT_INFO> ActInfoQueue;

typedef vector<int> IntArray;
// typedef struct Workflow_info
// {
	// int id;
	// string description;
// } WORKFLOW_INFO;

typedef enum
{
	IP_PUBLIC = 1,
	IP_PRIVATE = 0
} IP_TYPE;

struct InterfaceInfo
{
	string strJson;
	string strMac;
	IP_TYPE eType;
};



#define paraLogFile "BugLog"

