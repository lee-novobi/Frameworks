//#include "StdAfx.h"
#include "WorkFlowLoader.h"
#include "WorkFlow.h"
#include "../Processor/FunctionExecuter.h"
#include "../Controller/WorkFlowController.h"
#include "../Controller/AlertController.h"
#include "../Config/DBParameters.h"
#include "../Processor/DataLock.h"
#include "../Common/DBCommon.h"

#define nLimitAlerts 20

CWorkFlowLoader::CWorkFlowLoader(void)
{
	m_lockThread = new CDataLock();
}


CWorkFlowLoader::~CWorkFlowLoader(void)
{
	CFunctionExecuter::GetInstance()->ReleaseInstance();
	delete m_lockThread;
}

void CWorkFlowLoader::RecurElement(vector<int> vFlowId, vector<MA_RESULT> vResult, ActInfoQueue queueInfo, CWorkFlow* pWorkFlow)
{
	//boost::this_thread::sleep( boost::posix_time::milliseconds(100) );
	//int iChildArraySize, iNodeValue;
	int iActId;
	int i,j;
	iActId = -1;
	vector<int> vNextFlowId;
	vector<MA_RESULT> vNextActResult;
	METHOD_ID eMethodID;
	CONDITION_ID eConditionID;
	for(i = 0; i < vFlowId.size(); i++)
	{
		for(j = 0; j < queueInfo.size(); j++)
		{
			if(queueInfo[j].m_iFlowId == vFlowId[i])
			{
				iActId = queueInfo[j].m_iActId;
				if(queueInfo[j].m_iNextFlowId != -1)
				{
					vNextActResult.push_back(queueInfo[j].m_eResult);
					vNextFlowId.push_back(queueInfo[j].m_iNextFlowId);
				}
				
				eMethodID = queueInfo[j].m_eMethodID;
				eConditionID = queueInfo[j].m_eConditionID;
			}
		}
		cout<< "\nFlow ID : "<< vFlowId[i] <<endl;
		cout<< "iActId ID : "<< iActId <<endl;
		cout<<"Next size: " << vNextFlowId.size() << endl;
		cout<< "eMethodID : "<< eMethodID <<endl;
		cout<<"eConditionID: " << eConditionID << endl;
		cout<<"vNextFlowId.size(): " << vNextFlowId.size() << endl;
		if(iActId != -1)
			pWorkFlow->Insert(i, iActId, vResult[i], vNextFlowId.size(), eMethodID, eConditionID);
		if(!vNextFlowId.empty())
		{
			RecurElement(vNextFlowId, vNextActResult, queueInfo, pWorkFlow);
			vNextFlowId.clear();
			vNextActResult.clear();

		}
	}
	cout<<"out recur"<<endl;
}


CWorkFlow* CWorkFlowLoader::CreateWorkflow(ActInfoQueue queueInfo)
{
	//int iChildArraySize, iRootValue;
	int iFlowId, iActId, iRootId;
	vector<int> vNextFlowId;
	vector<MA_RESULT> vNextActResult;
	METHOD_ID eMethodID;
	CONDITION_ID eConditionID;
	CWorkFlow* pWorkFlow = new CWorkFlow();
	iActId = -1;
	// run root action: return a Result
	/*/
	iActId = Record[ACTION_TREE][ROOT_VALUE].numberInt();
	iResult = Record[ACTION_TREE][CHILD_ARRAY].Array().size();
	iNextActId = Record[ACTION_TREE][CHILD_ARRAY].Array().size();
	eMethodID = (METHOD_ID)Record[ACTION_TREE][METHOD_ID].numberInt();
	eConditionID = (CONDITION_ID)Record[ACTION_TREE][CONDITION_ID].numberInt(); */
	iRootId = 0;
	for(int i = 0; i < queueInfo.size(); i++)
	{
		if(queueInfo[i].m_iFlowId == iRootId)
		{
			iActId = queueInfo[i].m_iActId;
			if(queueInfo[i].m_iNextFlowId != -1)
			{
				vNextActResult.push_back(queueInfo[i].m_eResult);
				vNextFlowId.push_back(queueInfo[i].m_iNextFlowId);
			}
			//cout<< "eMethodID : "<< queueInfo[i].m_eMethodID <<endl;
			//cout<<"eConditionID: " << queueInfo[i].m_eConditionID << endl;
			eMethodID = queueInfo[i].m_eMethodID;
			eConditionID = queueInfo[i].m_eConditionID;
		}
	}
	cout<< "\nFlow ID : "<< iRootId <<endl;
	cout<< "iActId ID : "<< iActId <<endl;
	cout<<"Next size: " << vNextFlowId.size() << endl;
	cout<< "eMethodID : "<< eMethodID <<endl;
	cout<<"eConditionID: " << eConditionID << endl;
	cout<<"vNextFlowId.size(): " << vNextFlowId.size() << endl;
	
	if(iActId != -1)
		pWorkFlow->Insert(0, iActId, MA_RESULT_UNKNOWN, vNextFlowId.size(), eMethodID, eConditionID);
	
	if(!vNextFlowId.empty())
	{
		RecurElement(vNextFlowId, vNextActResult, queueInfo, pWorkFlow);
		vNextFlowId.clear();
		vNextActResult.clear();
	}

	cout<<"end CreateWorkflow"<<endl;
	pWorkFlow->Reset();
	return pWorkFlow;
}

void CWorkFlowLoader::LoadWorkFlow()
{
	string strHostPort, strKey;
	int pos;
	auto_ptr<DBClientCursor> WorkflowCursor, AlertCursor;
	BSONObj AlertRecord;
	ActInfoQueue queueActionInfo;
	pos = 0;
	strHostPort = HOST_NAME;
	strHostPort += ":";
	strHostPort += paraPort;

	//CAlertController oAlertController;
	CWorkFlowController oWFController;

	//if(!oAlertController.Connect(strHostPort ,paraUser, paraPassword, paraSource))
	//	return;
	//AlertCursor = oAlertController.FindAll();
	
	if(!oWFController.Connect("localhost" , "zabbix_admin", "Z@44ix0n3", "test"))
		return;
	//WorkflowCursor = oWFController.LoadWorkFlowData();
	cout<<"start LoadWorkFlow"<<endl;
	//while(AlertCursor->more())
	//{
		cout<<endl<<pos++<<endl;
		
		//AlertRecord = AlertCursor->next();
		//strKey = AlertRecord[KEY_].valuestrsafe();
		if(pos < nLimitAlerts)
		{
			//WorkflowRecord = oWFController.FindByKey(strKey);
			if(!oWFController.SelectQuery("SELECT actf.*,act.method,act.condition,act.description FROM (action_flow AS actf) LEFT JOIN (actions AS act) ON (act.id = actf.actionid) WHERE actf.workflowid = 1"))
			//if(!oWFController.SelectQuery("select * from action_flow"))
			{
				//oWFController.PrintResult();
				oWFController.GetResult(queueActionInfo);
				
				//CreateWorkflow(queueActionInfo);
				CFunctionExecuter::GetInstance()->AddWorkFlow(CreateWorkflow(queueActionInfo));
			}
		}
		//WorkflowRecord = WorkflowCursor->next();
		//CFunctionExecuter::GetInstance()->AddWorkFlow(CreateWorkflow(WorkflowRecord));
	//}
}