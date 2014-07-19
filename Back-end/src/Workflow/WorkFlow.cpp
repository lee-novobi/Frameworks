
// #include "PrototypeConfig.h"
// #ifndef LINUX
//#include "StdAfx.h"
// #endif

#include "Node.h"
#include "WorkFlow.h"
#include "WorkFlowData.h"


CWorkFlow::CWorkFlow(void)
{
	m_pRoot			= NULL;
	m_pCurrentNode	= m_pRoot;
	m_iFlagBack		= 0;
	//m_pData			= new CWorkFlowData();
}

CWorkFlow::CWorkFlow(string strDescription)
{
	m_pRoot			= NULL;
	m_pCurrentNode	= m_pRoot;
	m_iFlagBack		= 0;
	//m_pData			= new CWorkFlowData();
	m_strDescription = strDescription;
}

CWorkFlow::~CWorkFlow(void)
{
	cout<<"Delete CWorkFlow"<<endl;
	if (m_pRoot != NULL)
	{
		cout<<"Delete m_pRoot"<<endl;
		//delete m_pRoot;
	}	

	//delete m_pData;
}

void CWorkFlow::Insert(int iFlagBack,  int iActId, MA_RESULT eResult, int iChildArraySize, METHOD_ID eMethodID, CONDITION_ID eConditionID)
{

	CNode* pTempNode = new CNode(iActId, eResult, iChildArraySize, eMethodID, eConditionID);
	
	if(m_pRoot == NULL)
	{
		cout<<"insert root"<<endl;
		m_pRoot = pTempNode;
		m_pCurrentNode = m_pRoot;
	}
	else
	{
		if(iFlagBack == 0) // insert the first child of a node
		{
			m_pCurrentNode->AddChild(pTempNode);
			pTempNode->SetParent(m_pCurrentNode);
			m_pCurrentNode = pTempNode;
			//m_iFlagBack = iFlagBack;
			cout<<"insert 1"<<endl;
		}
		else if(iFlagBack >= 1) // go back to insert another branch
		{
			/*if(m_iFlagBack == 0)
			{
				m_pCurrentNode = m_pCurrentNode->GetParentNode();
				cout<<"insert 2"<<endl;
			}*/
			//else
			//{
				m_pCurrentNode = m_pCurrentNode->GetParentNode();
				while(m_pCurrentNode->GetChildArrNode().size() == m_pCurrentNode->GetChildArraySize())
				{
					m_pCurrentNode = m_pCurrentNode->GetParentNode();
				}
				cout<<"insert 2"<<endl;
			//}
			m_pCurrentNode->AddChild(pTempNode);
			pTempNode->SetParent(m_pCurrentNode);
			m_pCurrentNode = pTempNode;
			//m_iFlagBack = iFlagBack;
		}
	}
	
	cout<<"insert end"<<endl;
}

void CWorkFlow::Reset()
{
	m_pCurrentNode = m_pRoot;
}

CNode* CWorkFlow::GetActivatedNode(MA_RESULT eResult)
{
	CNode* pNode = NULL;

	while (m_pCurrentNode != NULL)
	{
		pNode = m_pCurrentNode->GetActivatedNode(eResult);
		// Found activated node
		if (pNode != NULL)
		{
			m_pCurrentNode = pNode;
			break;
		}
		else
		{
			// Get activated node from it's parent node
			break;
			m_pCurrentNode = m_pCurrentNode->GetParentNode();

			if(m_pCurrentNode == NULL)
			{
				cout<<"this is root node !!"<<endl;
				break;
			}
		}
	}	

	return pNode;
}

void CWorkFlow::OnAfterThreadExecute(CBaseThread* pThread)
{
	// Release thread
	cout<<"OnAfterThreadExecute"<<endl;
	delete pThread;
}


MA_RESULT CWorkFlow::Execute()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	CNode* l_pActivatedNote = NULL;
	
	cout<<endl<<"========"<<m_strDescription<<"========"<<endl;
	if(m_pCurrentNode != NULL)
	{
		eResult = m_pCurrentNode->Execute();
		do
		{
			l_pActivatedNote = GetActivatedNode(eResult);

			if (NULL == l_pActivatedNote)
			{
				cout<<"break CWorkFlow::Execute() !! "<<endl;
				break;
			}
			eResult = l_pActivatedNote->Execute();

		} while (l_pActivatedNote);
	}
	return eResult;
}

MA_RESULT CWorkFlow::ThreadExecute()
{
	cout<<"CWorkFlow::ThreadExecute()"<<endl;
	return Execute();
}