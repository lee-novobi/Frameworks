
// #include "PrototypeConfig.h"
// #ifndef LINUX
//	#include "StdAfx.h"
// #endif
#include "Node.h"
#include "../Action/Action.h"
#include "../Processor/FunctionInterface.h"
class CPingAction;
class CTelnetAction;
class CLoginWebAction;
class CLoginAppAction;
class CCheckCCUAction;


CNode::CNode(void)
{
	m_nCurrentIndex = 0;
	m_pAction = NULL;
	m_pParentNode = NULL;
}

CNode::CNode (int iActId, MA_RESULT eResult, int iChildArraySize, METHOD_ID eMethodID, CONDITION_ID eConditionID)
{	
	m_nCurrentIndex = 0;
	m_pAction = NULL;
	m_pParentNode = NULL;
	m_iActId = iActId;
	m_eResult = eResult;
	m_iChildArraySize = iChildArraySize;
	m_eConditionID = eConditionID;
	m_eMethodID = eMethodID;
}


CNode::~CNode(void)
{
	cout<<"delete CNode"<<endl;
	Destroy();
}

MA_RESULT CNode::Execute()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	cout<<"CNode::Execute()"<<endl;
	cout<<m_iActId<<endl;
	
	if(m_eConditionID != -1)
		eResult = CFunctionInterface::GetInstance()->CheckCondition(m_eConditionID);
	else if(m_eMethodID != -1)
		eResult = CFunctionInterface::GetInstance()->ExecuteFunction(m_eMethodID);
	return eResult;
}

void CNode::AddChild(CNode* Child)
{
	m_arrChildNode.push_back(Child);
}


void CNode::SetParent(CNode* Parent)
{
	m_pParentNode = Parent;
}

CNode* CNode::GetActivatedNode()
{
	CNode* pNode = NULL;
	if (m_nCurrentIndex < m_iChildArraySize)
	{
		pNode = m_arrChildNode[m_nCurrentIndex];
		cout<<"Currindex node: "<<m_nCurrentIndex<<endl;
		m_nCurrentIndex++;
	}
	else
		cout<<"RETURN NULL NODE"<<endl;
	return pNode;
}

CNode* CNode::GetActivatedNode(MA_RESULT eResult)
{
	CNode* pNode = NULL;
	for(int i = 0; i < m_iChildArraySize; i++)
		if(m_nCurrentIndex < m_iChildArraySize && eResult == m_arrChildNode[i]->GetResult())
		{
			cout<<"RETURN NODE"<<endl;
			m_nCurrentIndex++;
			return m_arrChildNode[i];
		}
		
	cout<<"RETURN NULL NODE"<<endl;
	return pNode;

}

void CNode::ReTravel()
{
	m_nCurrentIndex = 0;
}

void CNode::Destroy()
{
	// Destroy action
	if (m_pAction != NULL)
	{
		delete m_pAction;
	}
	cout<<"delete CNode"<<endl;
	// Destroy child nodes
	NodeArray::iterator it = m_arrChildNode.begin();

	while (it != m_arrChildNode.end())
	{
		delete *it;
		it++;
	}

	m_arrChildNode.clear();
}