
#include "../Common/Common.h"
#include "../Config/PrototypeConfig.h"

class CWorkFlow;
class CDataLock;

class CWorkFlowLoader
{
public:
	CWorkFlowLoader(void);
	~CWorkFlowLoader(void);
	void LoadWorkFlow();

private:
	CWorkFlow* CreateWorkflow(ActInfoQueue queueInfo);
	void RecurElement(vector<int> vNextActId, vector<MA_RESULT> vResult, ActInfoQueue queueInfo, CWorkFlow* pWorkFlow);
	CDataLock*	m_lockThread;

};

