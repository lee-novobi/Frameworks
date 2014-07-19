#include "../../Action.h"

class CTelnetAction:public CAction
{
public:
	CTelnetAction();
	~CTelnetAction();
	bool DoAct(const char* host, const char* port);
};