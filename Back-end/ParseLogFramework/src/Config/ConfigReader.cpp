#include "ConfigReader.h"
#include <sstream>

using namespace std;
using namespace boost;
using namespace property_tree;
/*
 * Constructor - Assign a file name
 */
CConfigReader::CConfigReader(const std::string& file_name)
{
	m_File = file_name;

	 try
    {
		read_ini(m_File, m_pt);
	}
    catch(std::exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

/*
 * Destructor -
 */
CConfigReader::~CConfigReader()
{

}

/*
 * update - Writes the updated configuration file.
 */
void CConfigReader::update(const std::string& strGroup, const std::string& strProperty, const std::string& strValue)
{
   string strQuery = strGroup + "." + strProperty;
   try{
	   m_pt.put(strQuery, strValue);
	   write_ini( m_File, m_pt );
   }
    catch(std::exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

/*
 * add - Add info into configuration file.
 */
void CConfigReader::add(const std::string& strGroup, const std::string& strProperty, const std::string& strValue)
{
   string strQuery = strGroup + "." + strProperty;
   try{
	   m_pt.add(strQuery, strValue);
	   write_ini( m_File, m_pt );
   }
    catch(std::exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

/*
 * load - Load value from configuration file.
 */
std::string CConfigReader::load(const std::string& strGroup, const std::string& strProperty)
{  
	string strQuery = strGroup + "." + strProperty;
	 try
    {
		read_ini(m_File, m_pt);
	}
    catch(std::exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	string strRes;
	try 
	{
		strRes =  m_pt.get<std::string>(strQuery);
	}
    catch(std::exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
	return strRes;
}