import os, re
import sys, math
from MongoDBModel import *
from Utility import Utilities
from datetime import datetime
from Config import CConfig
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
from bson.code import Code
from inspect import stack

class CMongodbController:
	def __init__(self):
		self._oConfig = CConfig()
		try:
			self._oConnectorMongodb			= Connection(self._oConfig.GetMongoDBUri(), self._oConfig.GetMongoDBPort())
			self._oDatabaseMongodb			= PymongoDatabase(self._oConnectorMongodb, self._oConfig.GetMongoDBSource())

		except Exception, exc:
			strErrorMsg = 'Mongodb Controller Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#**************************************************************
	# Function: GetChangesByName
	# Description: Get changes info by name to get collection name
	# Parameter: string name
	# Result: Dictionary
	#**************************************************************
	def  GetChangesByName(self, strName):
		dResult = dict()
		try:
			oChangesCollection = PymongoCollection(self._oDatabaseMongodb, "changes", False)
			dResult = oChangesCollection.find({'name': strName}, {'active':1, 'passive':1, '_id':0})[0]
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
		finally:
			return dResult;

	#************************************************************************
	# Function: SwitchChangesActive
	# Description: Switch active collection in document of changes collection
	# Parameter: name collection, name active, name passive
	#************************************************************************
	def  SwitchChangesActive(self, strName, strActive, strPassive):
		bResult = False;

		try:
			oChangesCollection = PymongoCollection(self._oDatabaseMongodb, "changes", False)
			if oChangesCollection.find({'name': strName}).count() == 1:
				oChangesCollection.update({ 'name': strName },
										  { '$set': {'active': strPassive, 'passive': strActive} })
				bResult = True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
		finally:
			return bResult

	#************************************************************************
	# Function: GetActiveCollection
	# Description: Get active collection by name
	# Parameter: string name collection
	# Result: Collection
	#************************************************************************
	def	 GetActiveCollection(self, strCollection):
		try:
			oActivePyCollection = None
			dChangesInfo = self.GetChangesByName(strCollection)
			strActive = dChangesInfo['active']
			oActivePyCollection = PymongoCollection(self.oDatabaseMongodb, strActive, False)
			return oActivePyCollection
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.oConfig)
			return None

	#***************************************************************
	# Function: SaveMaintenances
	# Description: Save maintenances info
	# Parameter: maintenances collection, maintenanceid, data dictionary
	#***************************************************************
	def  SaveMaintenances(self, oMaintenancesCollection, lMaintenanceId, lTimeperiodId, dMaintenanceInfo):
		try:
			if oMaintenancesCollection.find({'maintenanceid': lMaintenanceId}).count() == 0:
				oMaintenancesObject						= CMaintenances(oMaintenancesCollection)
				oMaintenancesObject['maintenanceid']	= lMaintenanceId
				oMaintenancesObject['timeperiodid']		= lTimeperiodId
				oMaintenancesObject['zbx_maintenanceid']= dMaintenanceInfo['zbx_maintenanceid']
				oMaintenancesObject['zbx_server_id']	= dMaintenanceInfo['zbx_server_id']
				oMaintenancesObject['name']				= dMaintenanceInfo['name']
				oMaintenancesObject['maintenance_type']	= dMaintenanceInfo['maintenance_type']
				oMaintenancesObject['active_since']		= dMaintenanceInfo['active_since']
				oMaintenancesObject['active_till']		= dMaintenanceInfo['active_till']
				oMaintenancesObject['timeperiod_type']	= dMaintenanceInfo['timeperiod_type']
				oMaintenancesObject['every']			= dMaintenanceInfo['every']
				oMaintenancesObject['month']			= dMaintenanceInfo['month']
				oMaintenancesObject['dayofweek']		= dMaintenanceInfo['dayofweek']
				oMaintenancesObject['day']				= dMaintenanceInfo['day']
				oMaintenancesObject['start_time']		= dMaintenanceInfo['start_time']
				oMaintenancesObject['period']			= dMaintenanceInfo['period']
				oMaintenancesObject['start_date']		= dMaintenanceInfo['start_date']
				oMaintenancesObject.save()
			else:
				oMaintenancesCollection.update({'maintenanceid': lMaintenanceId, 'timeperiodid': lTimeperiodId},
												{'$set': dMaintenanceInfo})

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#***************************************************************
	# Function: SaveMaintenancesHosts
	# Description: Save maintenances hosts info
	# Parameter: maintenances_hosts collection, maintenanceid, hostid, data dictionary
	#***************************************************************
	def  SaveMaintenancesHosts(self, oMaintenancesHostsCollection, lMaintenanceId, lHostId, dMaintenancesHostsInfo):
		try:
			if oMaintenancesHostsCollection.find({'maintenanceid': lMaintenanceId, 'hostid': lHostId}).count() == 0:
				oMaintenancesHostsObject					= CMaintenancesHosts(oMaintenancesHostsCollection)
				oMaintenancesHostsObject['maintenanceid']	= lMaintenanceId
				oMaintenancesHostsObject['hostid']			= lHostId
				oMaintenancesHostsObject['zbx_hostid']		= dMaintenancesHostsInfo['zbx_hostid']
				oMaintenancesHostsObject['zbx_server_id']	= dMaintenancesHostsInfo['zbx_server_id']

				oMaintenancesHostsObject.save()
			else:
				oMaintenancesHostsCollection.update({'maintenanceid': lMaintenanceId, 'hostid': lHostId},
												{'$set': dMaintenancesHostsInfo})

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#***************************************************************
	# Function: SaveMaintenancesGroups
	# Description: Save maintenances groups info
	# Parameter: maintenances_groups collection, maintenanceid, groupid, data dictionary
	#***************************************************************
	def  SaveMaintenancesGroups(self, oMaintenancesGroupsCollection, lMaintenanceId, lGroupId, dMaintenancesGroupsInfo):
		try:
			if oMaintenancesGroupsCollection.find({'maintenanceid': lMaintenanceId, 'groupid': lGroupId}).count() == 0:
				oMaintenancesGroupsObject					= CMaintenancesGroups(oMaintenancesGroupsCollection)
				oMaintenancesGroupsObject['maintenanceid']	= lMaintenanceId
				oMaintenancesGroupsObject['groupid']		= lGroupId
				oMaintenancesGroupsObject['zbx_groupid']	= dMaintenancesGroupsInfo['zbx_groupid']
				oMaintenancesGroupsObject['zbx_server_id']	= dMaintenancesGroupsInfo['zbx_server_id']

				oMaintenancesGroupsObject.save()
			else:
				oMaintenancesGroupsObject.update({'maintenanceid': lMaintenanceId, 'groupid': lGroupId},
												{'$set': dMaintenancesGroupsInfo})

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#***************************************************************
	# Function: SaveHosts
	# Description: Save hosts info
	# Parameter: maintenances_hosts collection, maintenanceid, hostid, data dictionary
	#***************************************************************
	def  SaveHosts(self, oHostsCollection, lHostId, dHostsInfo):
		try:
			if oHostsCollection.find({'hostid': lHostId}).count() == 0:
				oHostsObject					= CHosts(oHostsCollection)
				oHostsObject['hostid']			= lHostId
				oHostsObject['zbx_hostid']		= dHostsInfo['zbx_hostid']
				oHostsObject['zbx_server_id']	= dHostsInfo['zbx_server_id']

				oHostsObject.save()
			else:
				oHostsCollection.update({'hostid': lHostId},{'$set': dHostsInfo})

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)