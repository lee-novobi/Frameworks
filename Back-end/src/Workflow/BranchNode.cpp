
// #include "PrototypeConfig.h"
// #ifndef LINUX
//	#include "StdAfx.h"
// #endif
#include "BranchNode.h"
#include "Action.h"

CBranchNode::CBranchNode(void)
{
}

CBranchNode::~CBranchNode(void)
{
}

CNode* CBranchNode::GetActivatedNode()
{
	CNode* pNode = NULL;
	MA_RESULT eResult;

	if (m_pAction != NULL)
	{
		eResult = m_pAction->Do();
		if (MA_RESULT_SUCCESS == eResult)
		{
			// Get first child
			if (m_arrChildNode.size() > 0)
			{
				pNode = m_arrChildNode[0];
			}
		}
		else
		{
			// Get second child
			if (m_arrChildNode.size() > 1)
			{
				pNode = m_arrChildNode[1];
			}
		}
	}

	return pNode;
}