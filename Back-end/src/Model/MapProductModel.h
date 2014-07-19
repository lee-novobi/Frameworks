#pragma once
#include "MongodbModel.h"

class CMapProductModel:public CMongodbModel
{
protected:
	string m_strMapSrc, m_strMapProdSrc;
public:
	CMapProductModel(void);
	~CMapProductModel(void);
	Query GetMapProductBySrcProductQuery();
	
	void PrepareRecord();
	void DestroyData();
//=================================Set Get Propertise ==============================
	inline void SetMapSource(string strMapSrc)
	{
		m_strMapSrc = strMapSrc;
	}
	inline void SetMapSourceProduct(string strMapProdSrc)
	{
		m_strMapProdSrc = strMapProdSrc;
	}
};
