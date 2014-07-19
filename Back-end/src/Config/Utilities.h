#include <iostream>
#include <fstream>
using namespace std;

#ifndef UTILITIES_H
#define UTILITIES_H

class CConfig;

class CUtilities
{
	private:
		string m_strErrorFile;

	public: 
		CUtilities();
		virtual ~CUtilities();
		
		static void WriteErrorLog(CConfig* oConfig, const std::string& strErrorMsg);
};

#endif //UTILITIES_H