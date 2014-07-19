#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include "../Common/Common.h"
#include "ConfigFileParse.h"

class CLogParserData : public CConfigFileParse
{
public:	
	CLogParserData(string strLogFile, string strInfoFile);
	~CLogParserData(void);
	
	void* GetBuffer();
	int GetLength();	
	
	int GetPeriod();
	void SetPeriod(int);	
	void SetNewLogFile();
	string GetCurrLogFile();
	string GetCurrTail();
	void ClearMapMem();

	//void LoadLogConfig();
	//void LoadPosition();
protected:
	string m_strLogFile;
	string m_strInfoFile;
	string m_strCurrTail;
	int m_iPeriod;
	void* m_pBuffer;
	size_t m_szLength;
	off_t m_Position;
	struct stat m_sb;	
};