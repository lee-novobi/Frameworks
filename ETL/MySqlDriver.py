#Class MySqlConnection
#Define component of connection string mysql database
#and set string query for each condition
import time
from datetime import datetime

class CMySqlDriver:
	def __init__(self, strHost, strUser, strPass, strSource, strHostAlias, iPeriod, iZabbixServerId, strZabbixServerName, strZabbixVersion):
		self._strHost				= strHost
		self._strUser				= strUser
		self._strPassword			= strPass
		self._strSource				= strSource
		self._strHostAlias			= strHostAlias
		self._iPeriod				= iPeriod
		self._iZabbixServerId		= iZabbixServerId
		self._strZabbixServerName	= strZabbixServerName
		self._strZabbixVersion		= strZabbixVersion

	def GetHost(self):
		return self._strHost

	def GetUser(self):
		return self._strUser

	def GetPassword(self):
		return self._strPassword

	def GetSource(self):
		return self._strSource

	def GetHostAlias(self):
		return self._strHostAlias

	def GetPeriod(self):
		return self._iPeriod

	def GetZabbixServerId(self):
		return self._iZabbixServerId

	def GetZabbixServerName(self):
		return self._strZabbixServerName

	def GetZabbixVersion(self):
		return self._strZabbixVersion

	#Get string query by hosts
	def GetQueryByHosts(self):
		strSQL = '''
					SELECT h.hostid, maintenance_status
					FROM hosts h
				'''
		return strSQL

	#********************************************************
	# Function: GetQueryByMaintenances
	# Description: Get string query to get maintenances info
	# Result: String
	#********************************************************
	def GetQueryByMaintenances(self):
		timeLocal       = datetime.now()
		strUnixTime     = "%s" % (timeLocal.strftime("%s"))

		strSQL = '''
					SELECT 	DISTINCT m.maintenanceid, m.name,m.maintenance_type,m.active_since,m.active_till,t.timeperiodid,t.timeperiod_type
							,t.every,t.month,t.dayofweek,t.day,t.start_time, t.period, t.start_date
					FROM 	maintenances m, maintenances_windows ms, timeperiods t
					WHERE   m.maintenanceid = ms.maintenanceid
							AND t.timeperiodid = ms.timeperiodid
							AND m.active_since <=''' + strUnixTime + ''' AND m.active_till >''' + strUnixTime
		return strSQL

	#**************************************************************
	# Function: GetQueryByMaintenancesHosts
	# Description: Get string query to get maintenances hosts info
	# Result: String
	#**************************************************************
	def GetQueryByMaintenancesHosts(self):
		strSQL = '''
					SELECT DISTINCT m.maintenanceid, m.hostid
					FROM maintenances_hosts m
					UNION
					SELECT DISTINCT mg.maintenanceid, hg.hostid
					FROM maintenances_groups mg, hosts_groups hg
					WHERE mg.groupid = hg.groupid
				'''
		return strSQL

	#**************************************************************
	# Function: GetQueryByMaintenancesGroups
	# Description: Get string query to get maintenances groups info
	# Result: String
	#**************************************************************
	def GetQueryByMaintenancesGroups(self):
		strSQL = '''
					SELECT DISTINCT mg.maintenanceid, hg.hostid
					FROM maintenances_groups mg, hosts_groups hg
					WHERE mg.groupid = hg.groupid
				'''
		return strSQL

	#**************************************************************
	# Function: GetQueryHostsInfo
	# Description: Get string query to get hosts info
	# Result: String
	#**************************************************************
	def GetQueryHostsInfo(self):
		strSQL = '''
					SELECT hostid
					FROM hosts
				'''
		return strSQL

