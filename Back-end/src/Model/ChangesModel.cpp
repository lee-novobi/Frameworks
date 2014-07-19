#include "ChangesModel.h"
#include "../Common/DBCommon.h"
CChangesModel::CChangesModel(void)
{
	Init();
}

CChangesModel::~CChangesModel(void)
{
	DestroyData();
}

Query CChangesModel::GetCollectionByName(const char *pCharName)
{
	BSONObj p = BSON("name" << pCharName);
	Query queryCategory = Query(p);
	return queryCategory;
}

void CChangesModel::Init()
{
	m_strName = m_strActiveCollection = m_strPassiveCollection = "";
}

void CChangesModel::DestroyData()
{
	ReleaseBuilder();
}

void CChangesModel::PrepareRecord()
{
}
