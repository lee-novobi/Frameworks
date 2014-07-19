#include "HistoryUFloatLogParser.h"

CHistoryUFloatLogParser::CHistoryUFloatLogParser()
{
}

void CHistoryUFloatLogParser::ParseLog()
{
	string buffer;
	int fd;
    struct stat sb;
	off_t nCurPosition;
	size_t iLength;
	fd = open("./history_value_log_20130426_1535", O_RDONLY);
    if (fd == -1)
        cout << "open fail"<<endl;
    if (fstat(fd, &sb) == -1)           /* To obtain file size */
        cout << "fstat fail"<<endl;

	nCurPosition = 0; //position

	iLength = sb.st_size - nCurPosition;
	buffer = (char*)mmap(0, iLength, PROT_READ, MAP_PRIVATE, fd, 0);
	while(nCurPosition < iLength)
	{
		// Init database fields
		int iClock, iServerId, iValueType;
		long lHostId, lItemId;
		float fValueType;
		string strKey_, strValue;
		iClock = iServerId = 0;
		lHostId = lItemId = 0;
		fValueType	= 0;
		string temp;
		
		//Parse Clock
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		if(temp.compare("") != 0)
		{
			try
			{									
				iClock = atoi(temp.c_str());
			}
			catch(char *str)
			{
				cout<<str;
				continue;
			}
		}
		
		//Parse ServerId
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		if(temp.compare("") != 0)
		{
			try
			{									
				iServerId = atoi(temp.c_str());
			}
			catch(char *str)
			{
				cout<<str;
				continue;
			}
		}
		
		//Parse HostId
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		if(temp.compare("") != 0)
		{
			try
			{									
				lHostId = atol(temp.c_str());
			}
			catch(char *str)
			{
				cout<<str;
				continue;
			}
		}
		
		//Parse ItemId
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		if(temp.compare("") != 0)
		{
			try
			{									
				lItemId = atol(temp.c_str());
			}
			catch(char *str)
			{
				cout<<str;
				continue;
			}
		}
		
		//Parse Key
		strKey_ = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		
		//Parse Value
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		temp = GetToken(buffer, (int&)nCurPosition, (int)iLength);
		if(temp.compare("") != 0)
		{
			try
			{									
				fValueType = atof(temp.c_str());
			}
			catch(char *str)
			{
				cout<<str;
				continue;
			}
		}
		cout << iClock << " | " << iServerId << " | " << lHostId << " | " << lItemId << " | " << fValueType << " | " << strKey_ <<endl;
	}
	//cout << (char*)buffer << endl;
}

CHistoryUFloatLogParser::~CHistoryUFloatLogParser()
{
}

