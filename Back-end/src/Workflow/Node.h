#pragma once
#include "../Common/Common.h"

class CNode;
class CAction;
typedef vector<CNode*> NodeArray;

class CNode
{
public:
	CNode(void);
	CNode(int iActId, MA_RESULT eResult, int iChildArraySize, METHOD_ID eMethodID,  CONDITION_ID eConditionID);
	~CNode(void);

	MA_RESULT Execute();
	inline CNode* GetParentNode() { return m_pParentNode; }
	inline MA_RESULT GetResult() { return m_eResult; }
	inline NodeArray GetChildArrNode() { return m_arrChildNode; }
	inline int GetChildArraySize() { return m_iChildArraySize; }
	void AddChild(CNode* Child);
	void SetParent(CNode* Parent);
	virtual CNode*	GetActivatedNode();
	virtual CNode*	GetActivatedNode(MA_RESULT eResult);
	virtual void	ReTravel();

protected:
	virtual void Destroy();
protected:
	NodeArray	m_arrChildNode;
	CNode*		m_pParentNode;
	unsigned int		m_nCurrentIndex;
	int		m_iActId;
	MA_RESULT	m_eResult;
	int		m_iChildArraySize;
	METHOD_ID m_eMethodID;
	CONDITION_ID m_eConditionID;
	CAction*	m_pAction;
};
