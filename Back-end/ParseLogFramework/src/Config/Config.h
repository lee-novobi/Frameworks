#pragma once
#include "../Common/Common.h"

#ifndef CONFIG_H
#define CONFIG_H

class CConfigReader;

class CConfig
{
	protected:
		CConfigReader* m_oConfigReader;
		string strConfigFile;

	public: 
		CConfig();
		virtual ~CConfig();
		
		string GetErrorLog();
		string GetData(string strGroupName, string strPropertise);
		void AddData(string strGroupName, string strPropertise, string strValue);
		void UpdateData(string strGroupName, string strPropertise, string strValue);
};

#endif //CONFIG_H