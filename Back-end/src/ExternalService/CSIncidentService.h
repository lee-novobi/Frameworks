#include "BaseIncidentService.h"

class CJsonModel;
class CCSAlertController;

struct ResponseINCByCS
{
	int		iReturnCode;
	string	strListProcessingINC;
	string	strListClosingINC;
};

struct INCInfo
{
	string  strINCName;
	string  strINCCode;
	string  strProductID;
	string	strServerName;
	string	strErrorDescription;
	string	strErrorAnnoucement;
	int		iNumberOfCase;
	string	strImageLinks;
};

class CCSIncidentService:public CBaseIncidentService
{
public:
	CCSIncidentService(void);
	CCSIncidentService(string strCfgFile);
	~CCSIncidentService(void);
	
	string GetListIncident();
	
protected:
	void OpenListIncident(string strJsonData);
	bool ControllerConnect();

	CJsonModel *m_pJsonData;
	//CCSAlertController *m_pActiveCSAlertController;
	//CImpactLevelController *m_pActiveImpactLevelController;
}