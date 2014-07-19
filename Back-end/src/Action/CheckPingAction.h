#include "Action.h"

class CCheckPingAction:public CAction
{
public:
	CCheckPingAction();
	~CCheckPingAction();
	MA_RESULT Do();
};