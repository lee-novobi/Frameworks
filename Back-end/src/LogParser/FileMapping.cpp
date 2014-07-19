#include "FileMapping.h"

CFileMapping::CFileMapping()
{
	m_pBuffer = NULL;
	m_szLength = 0;
}

CFileMapping::~CFileMapping()
{
	
}

void CFileMapping::ReadFile(string strFileName)
{
	int fd;
	struct stat sb;
	stringstream strErrorMess;
	fd = open(strFileName.c_str(), O_RDONLY);
    if (fd == -1){
		strErrorMess << "CFileMapping::ReadFile -> GetBuffer: open fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
		return;
	}
    if (fstat(fd, &sb) == -1){           /* To obtain file size */
        strErrorMess << "CFileMapping::ReadFile -> GetBuffer: fstat fail : " << CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
		return;
	}

	m_szLength = sb.st_size;
	m_pBuffer = mmap(0, m_szLength, PROT_READ, MAP_PRIVATE, fd, 0);
	close(fd);
}

void CFileMapping::ClearMapMem()
{
	munmap(m_pBuffer,(int)m_szLength);
	m_pBuffer = NULL;
}