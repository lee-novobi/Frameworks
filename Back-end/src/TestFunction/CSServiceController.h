#include <vector>
#include <queue>
#include <stdlib.h>
#include <stdio.h>
#include <algorithm>
#include <string.h>
#include <iostream>
#include <fstream>
#include <string>
#include <sstream>
#include <time.h>
#include <map>
#include <iterator> // for ostream_iterator
using namespace std;

class CCSIncident
{
public:
	CCSIncident(void);
	~CCSIncident(void);
	string UpdateStatusINC();
	string GetCurrTime(const char* pFormat);
protected:
	char *m_pINCCode;	/* optional element of type xsd:string */
	short *m_pINCStatusID;	/* optional element of type xsd:short */
	char *m_pITSMCode;	/* optional element of type xsd:string */
	char *m_pCreatedBy;	/* optional element of type xsd:string */
	char *m_pComment;	/* optional element of type xsd:string */
	char *m_pITSMCloseDate;	/* optional element of type xsd:string */
};