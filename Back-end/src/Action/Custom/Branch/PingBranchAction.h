#include "../../Action.h"

class PingBranchAction:CAction
{
public:
	PingBranchAction();
	~PingBranchAction();
	void DoAct(bool pingRs);
};