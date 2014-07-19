#include "CheckLogin.h"

#define strZingLoginUrl "https://sso3.zing.vn/login"

#define strGunnyServerUrl "http://idgunny.zing.vn/index/game/id/15?old=1"
#define strGunnyData "u1=http://idgunny.zing.vn/index/server&fp=http://idgunny.zing.vn/index/"

#define strVuDeServerUrl "http://idvude.vn/?m=play&i=s49"
#define strVuDeData "u1=http://idvude.vn/?m=home&fp=http://idvude.vn"
#define strVuDeAK "e6b299bd1b5c438ebbd127a917ea8dce"

#define strVLCMServerUrl "http://idvlcm.zing.vn/?m=play&i=s233&name=PhiHo"
#define strVLCMData "u1=http://idvlcm.zing.vn/?m=home&fp=http://idvlcm.zing.vn"

#define strLongTuongServerUrl "http://id.longtuong.com.vn/servers.php"
#define strLongTuongData "u1=http://id.longtuong.com.vn/index.php&fp=http://id.longtuong.com.vn/index.php"
#define strLongTuongAK "215b71f9b8188d34c7ef9adfe02560b4"

#define strNgoaLongServerUrl "http://idngoalong.zing.vn/?m=play&i=haolong"
#define strNgoaLongData "u1=http://idngoalong.zing.vn/?m=listserver&fp=http://idngoalong.zing.vn"

int main(int argc, char* argv[])
{
	int iPos;
	string strPage;
	string strInfo, strTmp;
	//vector<string> v_strGunnyUrl;
	//v_strGunnyUrl.push_back(strGunnyServerUrl);
	
	if(argc != 8)
		return 0;
		
	//v_strGunnyUrl.push_back(argv[1]);
	//v_strGunnyUrl.push_back(argv[2]);
	strTmp = argv[3];
	strInfo += "u=" + strTmp;
	strTmp = argv[4];
	strInfo += "&p=" + strTmp;
	strTmp = argv[5];
	strInfo += "&x=45&y=28&pid=109&u1=" + strTmp;
	strTmp = argv[6];
	strInfo += "&fp=" + strTmp + "&apikey="; 
	strTmp = argv[7];
	strInfo += strTmp;
	cout<<"Info : "<<strInfo<<endl;
	
	//CCheckLogin *pCheck = new CCheckLogin(v_strGunnyUrl, strInfo, "", 0);
	CCheckLogin objCheck;
	strPage = objCheck.PostPage(argv[1], strInfo, "", 0);
	cout<<strPage<<endl;
	if(strPage.find("LOGIN_SUCCESSFULLY") != std::string::npos)
	{
		cout<<"Login Success !!"<<endl;
		
		if(strTmp.compare("null") != 0)
		{
			iPos = strPage.find("http:");
			strPage = strPage.substr(iPos);
			iPos = strPage.find("\n");
			strPage = strPage.substr(0,iPos-1);
			cout<<endl<<"Location: " << strPage<<endl;
			strPage = objCheck.PostPage(strPage, "", "", 0);
			//cout<<strPage<<endl;
		}
		
		cout<<"Server Link: " << argv[2] << endl;
		strPage = objCheck.GetPage(argv[2], "", 0);
		//cout<<strPage<<endl;
		if(strPage.find(argv[3]) != std::string::npos)
			
			cout<<"Server's Good !!"<<endl;
		else
			cout<<"Server's not found !!"<<endl;
	}
	else
	{
		cout<<"Login Fail !!"<<endl;
	}
	return 0;
}