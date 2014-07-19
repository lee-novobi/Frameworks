#pragma once
#include "../Common/Common.h"
#include <boost/property_tree/ptree.hpp>
#include <boost/property_tree/ini_parser.hpp>

using boost::property_tree::ptree;

#ifndef BASECONFIG_H
#define BASECONFIG_H

class CConfigReader
{
	private:
		string m_File;
		ptree m_pt;

	public: 
		CConfigReader();
		CConfigReader(const std::string& file_name);
		virtual ~CConfigReader();
		
		void update(const std::string& strGroup, const std::string& strProperty, const std::string& strValue);
		void add(const std::string& strGroup, const std::string& strProperty, const std::string& strValue);
		std::string load(const std::string& strGroup, const std::string& strProperty);
};

#endif//BASECONFIG_H