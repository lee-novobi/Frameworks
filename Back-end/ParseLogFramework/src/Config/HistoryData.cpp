#include "HistoryData.h"
#include "../Common/DBCommon.h"

/*
 * Constructor - Assign a file name
 */
CHistoryData::CHistoryData()
{

}

CHistoryData::CHistoryData(string strLogFile, string strInfoFile) : CLogParserData(strLogFile, strInfoFile)
{

}

/*
 * Destructor -
 */
CHistoryData::~CHistoryData()
{

}

void CHistoryData::SetProcessPosition(string strProcessId, int iPosition)
{
	stringstream ssPos, ssPosProperty;
	ssPos << iPosition;
	
	ssPosProperty << POS << "_" << strProcessId;
	UpdateData(INFO, ssPosProperty.str(), ssPos.str());
}

void CHistoryData::SetLastDatetime(string strDatetime)
{
	UpdateData(LAST, DATETIME, strDatetime);
}

string CHistoryData::GetLastDatetime()
{
	string line;
	line = GetData(LAST, DATETIME);
	if (line == "") 
	{
		line = CUtilities::GetDateSuffixHistory(m_iPeriod);
	}
	return line;
}

string CHistoryData::GetNextDatetime(string strLastDatetime)
{
    struct tm tm;
	string strNextDatetime;
	try{
	memset(&tm, 0, sizeof(struct tm));
	
	strptime(strLastDatetime.c_str(), "%Y%m%d_%H%M", &tm);
	tm.tm_min += m_iPeriod;
	mktime(&tm);
	}
	catch(exception& ex)
	{
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	
	strNextDatetime = CUtilities::FormatDateSuffixHistory(tm);
	return strNextDatetime;
}

string CHistoryData::GetPathDatePatternHistory(string strDatetimeSuffix)
{
	string strRes = GetCurrLogFile() + strDatetimeSuffix + "_";
	return strRes;
}

string CHistoryData::GetHistoryLogTail(string strProcessId)
{
	string line;
	string strProcessTail;
	strProcessTail = "Tail_" + strProcessId;
	line = GetData(INFO, strProcessTail);
	return line;
}

int CHistoryData::GetHistoryLogPosition(string strProcessId)
{
	string line;
	int iPos = 0;
	string strProcessPosition;
	strProcessPosition = "Position_" + strProcessId;
	line = GetData(INFO, strProcessPosition);
	if (line != "") 
	{
		try{
		iPos = atoi(line.c_str());
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return iPos;
}

void* CHistoryData::ReadFileBuffer(string strFile, int& iLength)
{
	struct stat sb;
	int fd;
	stringstream strErrorMess;
	fd = open(strFile.c_str(), O_RDONLY);
    if (fd == -1){
		strErrorMess << "GetBuffer: open fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
    if (fstat(fd, &sb) == -1)  {
		strErrorMess << "GetBuffer: fstat fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}

	m_szLength = sb.st_size;
	iLength = (int)m_szLength;
	m_pBuffer = mmap(0, m_szLength, PROT_READ, MAP_PRIVATE, fd, 0);
	close(fd);
	return m_pBuffer;
}

int CHistoryData::GetFileLength(string strFile)
{
	struct stat sb;
	int fd, iLength;
	fd = open(strFile.c_str(), O_RDONLY);
    if (fd == -1)
        //cout << "GetLength: open fail : " << strFile <<endl;
    if (fstat(fd, &sb) == -1)           // To obtain file size 
	{
        //cout << "GetLength: fstat fail : " << strFile <<endl;
		close(fd);
		return 0;
	}

	iLength = int(sb.st_size);
	close(fd);
	return iLength;
}

	