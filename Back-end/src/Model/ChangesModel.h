#pragma once
#include "MongodbModel.h"

class CChangesModel:public CMongodbModel
{
public:
	CChangesModel(void);
	~CChangesModel(void);
	
	void Init();
	void DestroyData();
	void PrepareRecord();
	Query GetCollectionByName(const char* pCharName);
	
	//Properties
	string	GetName() { return m_strName; }
	string	GetActiveCollection() { return m_strActiveCollection; }
	string	GetPassiveCollection() { return m_strPassiveCollection; }
	
	void SetName(const char* pCharName) { m_strName = pCharName; }
	void SetActiveCollection(const char* pCharActiveCollection) { m_strActiveCollection = pCharActiveCollection; }
	void SetPassiveCollection(const char* pCharPassiveCollection) { m_strPassiveCollection = pCharPassiveCollection; }
protected:
	string m_strName;
	string m_strActiveCollection;
	string m_strPassiveCollection;
};
