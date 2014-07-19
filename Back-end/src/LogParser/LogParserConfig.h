#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include "../Common/Common.h"
#include "Config.h"

class CLogParserData : public CConfig
{
public:
	CLogParserData();
	CLogParserData(string strLogFile, string strInfoFile);
	~CLogParserData(void);
	
	void* GetBuffer();
	int GetLength();
	int GetPosition();
	
	int GetPeriod();
	void SetPeriod(int);
	void SetPosition(int iPosition);
	void SetNewLogFile();
	string GetCurrLogFile();
	string GetCurrTail();

	//void LoadLogConfig();
	//void LoadPosition();
protected:
	string m_strLogFile;
	string m_strInfoFile;
	string m_strCurrTail;
	int m_iPeriod;
	void* m_Buffer;
	size_t m_szLength;
	off_t m_Position;
	struct stat m_sb;
	
};