#pragma once
#include "Node.h"

class CBranchNode :
	public CNode
{
public:
	CBranchNode(void);
	~CBranchNode(void);
	
	CNode*	GetActivatedNode();
};
