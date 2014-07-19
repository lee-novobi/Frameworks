/*
** Zabbix
** Copyright (C) 2000-2011 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/
#pragma once
#include "MongodbController.h"

class CTopMaintenanceModel;
class CConfigFileParse;
class CChangesController;
struct ConnectInfo;


class CMaintenanceController: public CMongodbController
{
public:
	CMaintenanceController(void);
	CMaintenanceController(string strCfgFile);
	~CMaintenanceController(void);
	int DayInMonth(int year, int mon);
	vector<long long> GetListHostMaintenance(ConnectInfo CInfo, long long lMaintenanceId);
	vector<long long> GetTopMaintenanceInfo(ConnectInfo CInfo, time_t now);
	void SaveTopMaintenanceInfo(ConnectInfo CInfo);
	void ComputeMaintenanceInfo(ConnectInfo CInfo);
	void RotateTopMaintenanceInfo(ConnectInfo CInfo);
protected:
	CConfigFileParse *m_pConfigFile;
};

