#include "SystemInfoLogParser.h"
#include "FileMapping.h"
#include "../Controller/ExternalAPIController.h"
#include "../Config/ConfigFileParse.h"
#include "../Controller/HistoryController.h"
#include "../Common/DBCommon.h"
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

CSystemInfoLogParser::CSystemInfoLogParser(void)
{
}

CSystemInfoLogParser::CSystemInfoLogParser(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CSystemInfoLogParser::~CSystemInfoLogParser(void)
{
	Destroy();
}

void CSystemInfoLogParser::ParseSystemInfo(const char* pBuffer, int iPosition, int iLength, long long lServerId, map<long long,string> &mapHostMacDict)
{
	int iFind;
	string strPriIPJson, strPubIPJson, strServerName, strSerialNo;
	string strVimKey;
	string strInterfaceBlock, strMacArr;
	stringstream strErrorMess;
	int iValuePos;
	
	iValuePos = iPosition;
	strVimKey = strServerName = strSerialNo = strMacArr ="";
	strPriIPJson = strPubIPJson = "[";
	
	//=========skip value block=========
	GetBlock((const char*)pBuffer, (int&)iValuePos, (int)iLength); 
	
	//=========Get Interface block=========
	strInterfaceBlock = GetBlock((const char*)pBuffer, (int&)iValuePos, (int)iLength);
	strInterfaceBlock = strInterfaceBlock.substr(1,strInterfaceBlock.length() - 2);
	//=========Get servername block=========
	strServerName = GetBlock((const char*)pBuffer, (int&)iValuePos, (int)iLength); 
	strServerName = CalculateServerName(strServerName);
	if(!strServerName.empty())
		m_pActiveHistoryController->ModelAppend(paraServerName,strServerName);
	
	//=========Get serial block=========
	strSerialNo = GetBlock((const char*)pBuffer, (int&)iValuePos, (int)iLength);
	
	//=========Get vetor interface info block=========
	cout << "\nstrInterfaceBlock : " << strInterfaceBlock << endl;
	cout<<"Serial : " << strSerialNo << endl;
	cout<<"Server Name : " << strServerName << endl;
	CalculateInterface(strInterfaceBlock, strPriIPJson, strPubIPJson, strMacArr);
	
	cout << "Private: " << strPriIPJson << endl;
	cout << "Public: " << strPubIPJson << endl;
	m_pActiveHistoryController->ModelAppend(paraPriInterface,strPriIPJson);
	m_pActiveHistoryController->ModelAppend(paraPubInterface,strPubIPJson);
	
	//=========Get VIM Key=========
	strVimKey = strSerialNo.substr(1, strSerialNo.length() - 2);
	cout<<"Mac arr : " << strMacArr << endl;
	cout<<"mapHostMacDict["<<lServerId<<"]: " << mapHostMacDict[lServerId] << endl;
	if(strSerialNo.find("VMware") != std::string::npos)
	{
		m_pActiveHistoryController->ModelAppend(VID,strSerialNo);
		if(strMacArr.length() > 1 && mapHostMacDict[lServerId].compare(strMacArr) != 0)
		{
			mapHostMacDict[lServerId] = strMacArr;
			strVimKey = CExternalAPIController::SNS_CollectVIDInfo(strMacArr);
			if(!strVimKey.empty())
				m_pActiveHistoryController->ModelAppend(paraServerKey,strVimKey);
			cout<<"=================================================================================================================\n";
		}
	}
	else
		m_pActiveHistoryController->ModelAppend(paraServerKey,strVimKey);
	cout << "VIM Key : " << strVimKey << endl;
					
	//=========Append info for updating=========
	m_pActiveHistoryController->ModelAppend(paraLastUpdated);
	m_pActiveHistoryController->Update();
}


vector< InterfaceInfo > CSystemInfoLogParser::GetInterfaceInfo(const char* Buffer)
{
	int iStart,iEnd;
	string strTmp, strName, strIP, strMac;
	vector< InterfaceInfo > vRes; // vector of InterfaceInfo
	//string Buff = "lo-127.0.0.1,103.23.157.123,00:00:00:00:00:00;eth0-103.23.157.123,00:50:56:B5:5D:C3;eth1-172.16.6.96,00:50:56:B5:5D:C4";
	vector<string> vInterface(CUtilities::SplitString(Buffer,";")); // split BlockInterface to vector
	for(int i = 0; i < vInterface.size(); i ++)
	{
		InterfaceInfo InfTmp;
		strTmp = vInterface[i];
		iEnd = strTmp.find("-");
		if(iEnd == std::string::npos)
			strName = "";
		else
			strName = strTmp.substr(0,iEnd);
		vector<string> vIpAdr(CUtilities::GetIPAddressCorrectly(strTmp));
		strMac = CUtilities::GetMacAddressCorrectly(strTmp);
		
		for(int j = 0; j < vIpAdr.size(); j++)
		{
			InfTmp.strJson += "{\"name\":\"" + strName + "\"";
			InfTmp.strJson += ",\"ip\":\"" + vIpAdr[j] + "\"";
			InfTmp.eType = GetIPType(vIpAdr[j]);
			InfTmp.strJson += ",\"mac\":\"" + strMac + "\"}";
			InfTmp.strMac = strMac;
			if(j != vIpAdr.size()-1)
				InfTmp.strJson += ",";
		}
		
		vRes.push_back(InfTmp);
	}
	return vRes;
}


string CSystemInfoLogParser::CalculateServerName(string strServerName)
{
	int iFind;
	remove(strServerName.begin(), strServerName.end(), ' ');
	iFind = strServerName.find(",");
	if(iFind != std::string::npos) // sub string follow 2 format of Server Name
		strServerName = strServerName.substr(1,iFind-1);
	else
	{
		iFind = strServerName.find("}");
		strServerName = strServerName.substr(1,iFind-1);
	}
	return strServerName;
}

void CSystemInfoLogParser::CalculateInterface(string strInterfaceBlock, string& strPriIPJson, string& strPubIPJson, string& strMacArr)
{	
	vector< InterfaceInfo > vIfInfo;
	stringstream strErrorMess;
	int iFind;
	try{
		vIfInfo = GetInterfaceInfo(strInterfaceBlock.c_str());
	}
	catch(exception& ex)
	{	
		strErrorMess.str(string());
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
		CUtilities::WriteErrorLog(m_pConfigFile, strErrorMess.str());
	}
	for(int j = 0; j < vIfInfo.size(); j++)
	{
		//=========Get IP=========
		if(vIfInfo[j].eType == IP_PRIVATE)
			strPriIPJson += vIfInfo[j].strJson + ",";
		else
			strPubIPJson += vIfInfo[j].strJson + ",";
			
		//=========Get Mac Array=========
		
		if(vIfInfo[j].strMac.compare(INVALID_MAC_ADDRESS) != 0 
			&& vIfInfo[j].strMac.find(SPEC_MAC_ADDRESS) != std::string::npos) // if Mac content "00:50:56"
			strMacArr += vIfInfo[j].strMac;
		if(j != vIfInfo.size() -1)
		{
			if(strMacArr.length() > 1)
				strMacArr += ",";
		}
							
	}

	strPriIPJson += "]";
	strPubIPJson += "]";
	
	iFind = strPriIPJson.find(",]");
	if(iFind != std::string::npos)
		strPriIPJson.replace(iFind,2,"]");	
		
	iFind = strPubIPJson.find(",]");
	if(iFind != std::string::npos)
		strPubIPJson.replace(iFind,2,"]");
}