#include "CheckCCUAction.h"
#include "../Controller/CheckCCUController.h"
#include "../Config/DBParameters.h"
#include "../Common/Common.h"

#define SecInMonth 2592000
#define SecInDay 86400
#define queryCreateTempTable "CREATE TEMPORARY TABLE temptable(serverkey VARCHAR(25),clock INT, ccu INT)"
#define querySelectTempTable "SELECT * FROM temptable"
#define queryDropTempTable "DROP TABLE temptable"
#define StoreProcedureName "HHHgetCCUBySerialNumber"
#define	CCUfileName "CCU.txt"
#define	CCUfilePrevName "CCUPrevCmp.txt"	

CCheckCCUAction::CCheckCCUAction()
{
	m_iPrevCCU = 0;
	m_iHighestCCU = 0;
}

CCheckCCUAction::CCheckCCUAction(string strAlertTime, string strServerKey, int nMonBack)
{
	stringstream ssTime;
	m_iPrevCCU = 0;
	m_iHighestCCU = 0;
	m_strServerKey = strServerKey;
	m_tsCheckCCUTime.m_strEndTimeStamp = strAlertTime;
	ssTime << (atoi(strAlertTime.c_str()) - SecInMonth);
	m_tsCheckCCUTime.m_strStartTimeStamp = ssTime.str();
}
CCheckCCUAction::CCheckCCUAction(int Y, int M, int D, int H, int Mi, int S, string strServerKey, int nMonBack)
{
	m_iPrevCCU = 0;
	m_iHighestCCU = 0;
	m_strServerKey = strServerKey;
	m_tsCheckCCUTime = GetTimeStamp(Y,M,D,H,Mi,S,nMonBack);
}

CCheckCCUAction::~CCheckCCUAction()
{
}

TIMESTAMP CCheckCCUAction::GetTimeStamp(int Y, int M, int D, int H, int Mi, int S, int nMonBack)
{
	TIMESTAMP tsResult;
	stringstream ssTime;
	struct tm tmDateTime;
	time_t time;
	string strResult;
	tmDateTime.tm_year = Y - 1900;
	tmDateTime.tm_mon = M - 1;
	tmDateTime.tm_mday  = D;
	tmDateTime.tm_hour  = H;
	tmDateTime.tm_min  = Mi;
	tmDateTime.tm_sec  = S;
	time = mktime(&tmDateTime);
	//cout<<time<<endl;
	ssTime << (int)time;
	tsResult.m_strEndTimeStamp = ssTime.str();
	ssTime.str("");
	ssTime << (int)time - SecInMonth*nMonBack;
	tsResult.m_strStartTimeStamp = ssTime.str();
	return tsResult;
}

void CCheckCCUAction::LoadCCUInfo()
{
	int iStart, iEnd;
	string strQuery,strTemp;
	
	CCheckCCUController objCCUContrl;
	if(!objCCUContrl.Connect(paraSqlHost, paraSqlUser, paraSqlPassword, paraSqlDBName))
		return;

	objCCUContrl.Query(queryCreateTempTable);
	

	cout<< m_tsCheckCCUTime.m_strStartTimeStamp << endl;
	
	cout<< m_tsCheckCCUTime.m_strEndTimeStamp << endl;
		//36H2F2S
	strTemp = StoreProcedureName;
	strQuery = "call " + strTemp + "('" + m_strServerKey + "'," + m_tsCheckCCUTime.m_strStartTimeStamp + "," + m_tsCheckCCUTime.m_strEndTimeStamp + ")";
	objCCUContrl.Query(strQuery.c_str());

	if(!objCCUContrl.SelectQuery(querySelectTempTable))
		objCCUContrl.GetResult(m_queueCCUInfo);

	objCCUContrl.Query(queryDropTempTable);
}


MA_RESULT CCheckCCUAction::Do()
{	
	MA_RESULT CCUResult = MA_RESULT_UNKNOWN;
	CCU_INFO objCCUInfo;
	int iDown, iPerDay;
	int nDayInLog, nDownTime, nDownRegular, nDownDay, nDownWeek, nDown2Week, nDown3Week, nDownMon;
	bool bCanUpdate;
	ofstream CCUfile (CCUfileName);
	ofstream CCUfilePrev (CCUfilePrevName);

	bCanUpdate = true;
	iDown = nDayInLog = nDownTime = nDownRegular = nDownDay = nDownWeek = nDown2Week = nDown3Week = nDownMon = iPerDay = 0;

	LoadCCUInfo();

	//while (m_queueCCUInfo.size() > 0)
	for(int i=0; i<m_queueCCUInfo.size(); i++) 
	{
		objCCUInfo = m_queueCCUInfo[i];//m_queueCCUInfo.front();
		
		//////////////////////////////////////////////OUT FILE///////////////////////////////////////////////
		for(int nCol = 0;nCol < objCCUInfo.m_iCCU/10;nCol++)
		{
			CCUfile << " ";
			CCUfile.flush();
			CCUfilePrev << " ";
			CCUfilePrev.flush();
		}

        CCUfile << objCCUInfo.m_iCCU<< " | " << objCCUInfo.m_iClock << " | " ;
		CCUfile.flush();
		CCUfilePrev << objCCUInfo.m_iCCU<< " | " << objCCUInfo.m_iClock << " | " ;
		CCUfilePrev.flush();

		///////////////////////////////////
		if( m_iPrevCCU !=0 )
		{
			iDown = (((m_iPrevCCU - objCCUInfo.m_iCCU)*100)/m_iPrevCCU);// Down Percent compare with previous CCU value
			CCUfilePrev << iDown << "%";
			CCUfilePrev.flush();
		}
		m_queueDownPerPrev.push_back(iDown);
		////////////////////////////


		///////////////////////////////////
		if( m_iHighestCCU !=0 )
		{
			iDown = (((m_iHighestCCU - objCCUInfo.m_iCCU)*100)/m_iHighestCCU);// Down Percent compare with highest CCU value
			CCUfile << iDown << "%";
			CCUfile.flush();
		}
		m_queueDownPer.push_back(iDown);

		CCUfile << endl;
		CCUfile.flush();
		CCUfilePrev << endl;
		CCUfilePrev.flush();

		//////////////////////////////////////////LOGIC UPDATE HIGHEST CCU VALUE///////////////////////////////////////////////
		if(m_iPrevCCU < objCCUInfo.m_iCCU) // Go up
			if( objCCUInfo.m_iCCU > m_iHighestCCU			// Update Highest CCU when current CCU higher than Highest 
						|| (objCCUInfo.m_iCCU > m_iHighestCCU/2 && bCanUpdate) )  // Or higher than Highest / 2 and flag bCanUpdate is true
				m_iHighestCCU = objCCUInfo.m_iCCU;
		if(m_iPrevCCU > objCCUInfo.m_iCCU)
		{
			if( (m_iHighestCCU - objCCUInfo.m_iCCU) > m_iHighestCCU/2 )  // turn on bCanUpdate flag when CCU Down over 1/2 Highest CCU
				bCanUpdate = true;
			else
				bCanUpdate = false;
		}
		////////////////////////////////////
		

		m_iPrevCCU = objCCUInfo.m_iCCU;
		//m_queueCCUInfo.erase(m_queueCCUInfo.begin());
	}

	

	for(int k=m_queueDownPerPrev.size()-2; k>=0; k--) 
	{
		iPerDay += (m_queueCCUInfo[k+1].m_iClock - m_queueCCUInfo[k].m_iClock);
		//cout<<m_queueCCUInfo[k+1].m_iClock << " - " << m_queueCCUInfo[k].m_iClock << " = " << iPerDay << endl;
		if((m_queueDownPerPrev.back()/10) <= (m_queueDownPerPrev[k]/10) + 1 && iPerDay >= SecInDay)
		//if((m_queueDownPerPrev.back()/10) >= 4 && (m_queueDownPerPrev[k]/10) >= 4 && iPerDay >= SecInDay)
		{
			nDownTime++;
			if(iPerDay%SecInDay == 0)
			{
				if(iPerDay/SecInDay  >= 30)
					nDownMon++;
				else if(iPerDay/SecInDay  >= 21)
					nDown3Week++;
				else if(iPerDay/SecInDay  >= 14)
					nDown2Week++;
				else if(iPerDay/SecInDay  >= 7)
					nDownWeek++;
				else if(iPerDay/SecInDay  >= 1)
					nDownDay++;
			}
			nDownRegular+=iPerDay/SecInDay;

			cout << "line: " << k + 1 << " | " << m_queueCCUInfo[k].m_iClock << " | " << iPerDay << " Days: " <<  iPerDay/SecInDay << ": " << m_queueDownPerPrev.back() << "% == " << m_queueDownPerPrev[k] << "%" << endl;
			iPerDay = 0;
		}
	}
	cout<<"Down : "<< nDownTime << endl;
	if(!m_queueCCUInfo.empty())
	{
		nDayInLog = (m_queueCCUInfo.back().m_iClock - m_queueCCUInfo.front().m_iClock)/SecInDay;
		if(nDownDay >= nDayInLog/2 && nDownDay !=0) // Per day is happen every day and more than 1/2 num of days
			cout<<"Per Day"<<endl;
		else if(nDownWeek >= nDayInLog/7 && nDownWeek != 0)
			cout<<"Per Week"<<endl;
		else if(nDown2Week >= nDayInLog/14 && nDown2Week != 0)
			cout<<"Per 2 Weeks"<<endl;
		else if(nDown3Week >= nDayInLog/21 && nDown3Week != 0)
				cout<<"Per 3 Weeks"<<endl;
		else if(nDownMon >= nDayInLog/30 && nDownMon != 0)
				cout<<"Per Month"<<endl;
		else if(nDownTime >= nDayInLog/4 && nDownRegular >= (nDayInLog*2)/3) // Regular is happen more than 1/4 num of days and 2/3 of days through
			cout<<"Regular !!! \nPlease check again !!!"<<endl;
		else
			cout<< "Incident !!!" << endl;
	}
	else
		cout<<"No CCU Log !!!" << endl;
	CCUfile.close();
	CCUfilePrev.close();
	return CCUResult;
}