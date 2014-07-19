#include "LogParserData.h"
#include "ConfigReader.h"
#include "../Common/DBCommon.h"
using std::stringstream;
#define FILE_LENGTH sizeof(int)

CLogParserData::CLogParserData()
{
	m_pBuffer = NULL;
}

CLogParserData::CLogParserData(string strLogFile, string strInfoFile)
{
	m_pBuffer = NULL;
	m_strCurrTail = " ";
	m_strLogFile = strLogFile;
	m_oConfigReader = new CConfigReader(strInfoFile);
}

int CLogParserData::GetPeriod()
{
	return m_iPeriod;
}

void CLogParserData::SetPeriod(int iPeriod)
{
	m_iPeriod = iPeriod;
}

void CLogParserData::SetNewLogFile()
{
	stringstream strCurrTail;
	time_t t = time(NULL);
	struct tm tm = *localtime(&t);
	//========get current day time=======
	strCurrTail << tm.tm_year + 1900;
	if(tm.tm_mon < 9)
		strCurrTail << '0';
	strCurrTail << tm.tm_mon + 1;
	if(tm.tm_mday < 10)
		strCurrTail << '0';
	strCurrTail << tm.tm_mday;
	m_strCurrTail = strCurrTail.str();
	//================================
	UpdateData(INFO,TAIL,m_strCurrTail);
	UpdateData(INFO,POS,"0");
}

string CLogParserData::GetCurrLogFile()
{
	string strRes;
	if(strcmp(m_strCurrTail.c_str(), " ") == 0)
		strRes = m_strLogFile;
	else
		strRes = m_strLogFile + GetCurrTail();
	return strRes;
}

string CLogParserData::GetCurrTail()
{
	string line;
	line = GetData(INFO, TAIL);
	return line;
}

void* CLogParserData::GetBuffer()
{
	int fd;
	stringstream strErrorMess;
	string strLogFile = GetCurrLogFile();
	fd = open(strLogFile.c_str(), O_RDONLY);
    if (fd == -1){
		strErrorMess << "GetBuffer: open fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
    if (fstat(fd, &m_sb) == -1){           /* To obtain file size */
        strErrorMess << "GetBuffer: fstat fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}

	m_szLength = m_sb.st_size;
	m_pBuffer = mmap(0, m_szLength, PROT_READ, MAP_PRIVATE, fd, 0);
	close(fd);
	return m_pBuffer;
}

int CLogParserData::GetPosition()
{
	string line;
	line = GetData(INFO,POS);
	m_Position = atoi(line.c_str());
	return (int)m_Position;
}

void CLogParserData::SetPosition(int iPosition)
{
	stringstream strPos;
	strPos << iPosition;
	UpdateData(INFO, POS, strPos.str());
}

int CLogParserData::GetLength()
{
	int fd;
	stringstream strErrorMess;
	string strLogFile = GetCurrLogFile();
	fd = open(strLogFile.c_str(), O_RDONLY);
	 if (fd == -1){
		strErrorMess << "GetBuffer: open fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
    if (fstat(fd, &m_sb) == -1){           /* To obtain file size */
        strErrorMess << "GetBuffer: fstat fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
		close(fd);
		return 0;
	}

	m_szLength = m_sb.st_size;
	close(fd);
	return (int)m_szLength;
}

void CLogParserData::ClearMapMem()
{
	munmap(m_pBuffer,(int)m_szLength);
}

CLogParserData::~CLogParserData(void)
{
	munmap(m_pBuffer,(int)m_szLength);
	delete m_oConfigReader;
}
