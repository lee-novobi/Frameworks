#include "Action.h"

class CCheckTelnetAction:public CAction
{
public:
	CCheckTelnetAction();
	~CCheckTelnetAction();
	MA_RESULT Do(const char* host, const char* port);
};