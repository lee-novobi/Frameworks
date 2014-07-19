#include "../../Action.h"

class CPingAction:public CAction
{
public:
	CPingAction();
	~CPingAction();
	bool DoAct(const char* cmd);
};