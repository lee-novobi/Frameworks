#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include "../Common/Common.h"

class CFileMapping
{
public:
	CFileMapping();
	~CFileMapping();
	
	inline void* GetBuffer() { return m_pBuffer; }
	inline int GetLength() { return (int)m_szLength; }
	
	void ReadFile(string strFileName);
	void ClearMapMem();
	
protected:
	void* m_pBuffer;
	size_t m_szLength;
};