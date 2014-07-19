#pragma once
#include "MongodbModel.h"

class CImpactLevelModel:public CMongodbModel
{
protected:
	string m_strSourceForm;
	int m_iNumOfCase;
public:
	CImpactLevelModel(void);
	~CImpactLevelModel(void);
	Query GetImpactLevelByCaseNumQuery();
	
	void PrepareRecord();
	void DestroyData();
//=================================Set Get Propertise ==============================
	inline void SetSourceForm(string strSourceForm)
	{
		m_strSourceForm = strSourceForm;
	}
	inline void SetNumOfCase(int iNumOfCase)
	{
		m_iNumOfCase = iNumOfCase;
	}
};
