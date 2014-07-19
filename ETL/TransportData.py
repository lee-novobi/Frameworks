# encoding: utf-8
# Description: Class controller connect mysql server and mongodb server
# Get data from mysql server to push into mongodb

import MongoDBModel
from Config import CConfig
from Utility import Utilities
from datetime import datetime
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
from MySqlDriver import CMySqlDriver
from MySqlConnection import CMySqlConnection
from MongoDBController import CMongodbController

#+ Add IP Information
import re, math, string
import sys
from inspect import stack

# class ZabbixController
# Collection data from mysql to mongodb
class CTransportData(object):
	def __init__(self):
		# Create connector mysql object
		self._oConfig = CConfig()
		try:
			# Create config object
			self._oMongodbController 		= CMongodbController()
			# Create connector mongokit object
			self._oConnectorMongodb			= Connection(self._oConfig.GetMongoDBUri(), self._oConfig.GetMongoDBPort())
			self._oDatabaseMongodb			= PymongoDatabase(self._oConnectorMongodb, self._oConfig.GetMongoDBSource())
		except Exception, exc:
			strErrorMsg = 'Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*************************************************
	# Function: Load All Connection
	# Description: Load all connection to source db
	#*************************************************
	def  LoadAllConnection(self):
		try:
			self._arrMySqlConnector = []
			self._arrMySqlDriver 	= []
			for oMySqlDriver in self._oConfig.GetArrayMySqlDriver():
				oConnector  = None
				oConnector	= CMySqlConnection()
				if oConnector.Connect(oMySqlDriver):
					self._arrMySqlConnector.append(oConnector)
					self._arrMySqlDriver.append(oMySqlDriver)

		except Exception, exc:
			strErrorMsg = 'Load All Connection Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#***********************************************
	# Function: GetAllMySqlConnection
	# Description: Get array all mysql connection
	# Result: Return array Connector
	#***********************************************
	def  GetAllMySqlConnection(self):
		return self._arrMySqlConnector

	#***********************************************
	# Function: GetAllMySql
	# Description: Get all driver mysql server
	#***********************************************
	def  GetAllMySqlDriver(self):
		return self._arrMySqlDriver

	def  CloseAllMySqlConnection(self):
		try:
			for oConnection in self.GetAllMySqlConnection():
				oConnection.Close()
			self._arrConnector = []

		except Exception, exc:
			strErrorMsg = 'Close Connection Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*****************************************************************
	# Function: SynchronizeHostInfo
	# Description: synchronize host info with zabbix QT & HL
	#*****************************************************************
	def  SynchronizeHostInfo(self):
		try:
			iNumberServer 		= len(self.GetAllMySqlConnection())
			iAmountConnection 	= len(self.GetAllMySqlDriver())

			if iNumberServer == iAmountConnection:
				oMaAlertsCollection = PymongoCollection(self._oDatabaseMongodb, "monitoring_assistant_alerts", False)

				for i in range (0, iNumberServer):
					oResult 		= None
					oConnector		= None

					oConnector  	= self.GetAllMySqlConnection()[i]
					strSQL 			= self.GetAllMySqlDriver()[i].GetQueryByHosts()
					oResult 		= oConnector.QueryAllData(strSQL) #Query Data from mysql server
					iZbxServerId 	= self.GetAllMySqlDriver()[i].GetZabbixServerId()

					if oResult is not None:
						for row in oResult:
							iHostId			   = long(row['hostid'])
							iServerId 		   = long(((iHostId - 10000) * 256) + iZbxServerId)
							iMaintenanceStatus = int(row['maintenance_status'])

							#print 'ServerId:%s -zbx:%s - host:%s - maintenance:%s' % (iServerId, iZbxServerId, iHostId, iMaintenanceStatus)
							oMaAlertsCollection.update({'zabbix_server_id': iServerId},
													{'$set':{'zbx_maintenance': iMaintenanceStatus}},
													upsert=False,
													multi=True)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*****************************************************************
	# Function: PushMaintenancesInfo
	# Description: push data maintenances info from zabbix QT & HL
	#*****************************************************************
	def  PushMaintenancesInfo(self):
		try:
			# Get groups backup in zabbix_changes collection by name
			strCollectionname = "maintenances"
			dChangesInfo = self._oMongodbController.GetChangesByName(strCollectionname)

			if len(dChangesInfo) > 0:
				iNumberServer 		= len(self.GetAllMySqlConnection())
				iAmountConnection 	= len(self.GetAllMySqlDriver())

				if iNumberServer == iAmountConnection:
					strPassiveName = dChangesInfo['passive']
					oPassiveCollection = PymongoCollection(self._oDatabaseMongodb, strPassiveName, False)
					oPassiveCollection.remove()

					iRunSuccessful = 0

					for i in range (0, iNumberServer):
						oResult 		= None
						oConnector		= None

						oConnector  	= self.GetAllMySqlConnection()[i]
						strSQL 			= self.GetAllMySqlDriver()[i].GetQueryByMaintenances()
						oResult 		= oConnector.QueryAllData(strSQL) #Query Data from mysql server
						iZbxServerId 	= self.GetAllMySqlDriver()[i].GetZabbixServerId()

						if oResult is not None:
							for row in oResult:
								lMaintenanceId	= long(row['maintenanceid'])
								lMaintenanceId 	= long((lMaintenanceId * 256) + iZbxServerId)
								lTimeperiodId	= long(row['timeperiodid'])
								lTimeperiodId	= long((lTimeperiodId * 256) + iZbxServerId)

								dMaintenanceInfo = dict();
								dMaintenanceInfo['zbx_maintenanceid'] 	= long(row['maintenanceid'])
								dMaintenanceInfo['zbx_server_id']		= iZbxServerId
								dMaintenanceInfo['name']				= row['name']
								dMaintenanceInfo['maintenance_type']	= int(row['maintenance_type'])
								dMaintenanceInfo['active_since']		= int(row['active_since'])
								dMaintenanceInfo['active_till']			= int(row['active_till'])
								dMaintenanceInfo['timeperiod_type']		= int(row['timeperiod_type'])
								dMaintenanceInfo['every']				= int(row['every'])
								dMaintenanceInfo['month']				= int(row['month'])
								dMaintenanceInfo['dayofweek']			= int(row['dayofweek'])
								dMaintenanceInfo['day']					= int(row['day'])
								dMaintenanceInfo['start_time']			= int(row['start_time'])
								dMaintenanceInfo['period']				= long(row['period'])
								dMaintenanceInfo['start_date']			= int(row['start_date'])
								self._oMongodbController.SaveMaintenances(oPassiveCollection, lMaintenanceId, lTimeperiodId, dMaintenanceInfo)

							iRunSuccessful += 1

					if 	iNumberServer == iRunSuccessful:
						self._oMongodbController.SwitchChangesActive(strCollectionname, dChangesInfo['active'], dChangesInfo['passive'])

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*****************************************************************
	# Function: PushMaintenancesHostsInfo
	# Description: push data maintenances hosts info from zabbix QT & HL
	#*****************************************************************
	def  PushMaintenancesHostsInfo(self):
		try:
			# Get groups backup in zabbix_changes collection by name
			strCollectionname = "maintenances_hosts"
			dChangesInfo = self._oMongodbController.GetChangesByName(strCollectionname)

			if len(dChangesInfo) > 0:
				iNumberServer 		= len(self.GetAllMySqlConnection())
				iAmountConnection 	= len(self.GetAllMySqlDriver())

				if iNumberServer == iAmountConnection:
					strPassiveName = dChangesInfo['passive']

					oPassiveCollection = PymongoCollection(self._oDatabaseMongodb, strPassiveName, False)
					oPassiveCollection.remove()

					iRunSuccessful = 0

					for i in range (0, iNumberServer):
						oResult 		= None
						oConnector		= None

						oConnector  	= self.GetAllMySqlConnection()[i]
						strSQL 			= self.GetAllMySqlDriver()[i].GetQueryByMaintenancesHosts()
						oResult 		= oConnector.QueryAllData(strSQL) #Query Data from mysql server
						iZbxServerId 	= self.GetAllMySqlDriver()[i].GetZabbixServerId()

						if oResult is not None:
							for row in oResult:
								lMaintenanceId	= long(row['maintenanceid'])
								lMaintenanceId 	= long((lMaintenanceId * 256) + iZbxServerId)
								lHostId			= long(row['hostid'])
								lHostId			= long(((lHostId - 10000) * 256) + iZbxServerId)

								dMaintenancesHostsInfo = dict();
								dMaintenancesHostsInfo['zbx_hostid'] 	= long(row['hostid'])
								dMaintenancesHostsInfo['zbx_server_id']	= iZbxServerId

								self._oMongodbController.SaveMaintenancesHosts(oPassiveCollection, lMaintenanceId, lHostId, dMaintenancesHostsInfo)

							iRunSuccessful += 1

					if 	iNumberServer == iRunSuccessful:
						self._oMongodbController.SwitchChangesActive(strCollectionname, dChangesInfo['active'], dChangesInfo['passive'])

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*****************************************************************
	# Function: PushMaintenancesGroupsInfo
	# Description: push data maintenances groups info from zabbix QT & HL
	#*****************************************************************
	def  PushMaintenancesGroupsInfo(self):
		try:
			# Get groups backup in zabbix_changes collection by name
			strCollectionname = "maintenances_groups"
			dChangesInfo = self._oMongodbController.GetChangesByName(strCollectionname)

			if len(dChangesInfo) > 0:
				iNumberServer 		= len(self.GetAllMySqlConnection())
				iAmountConnection 	= len(self.GetAllMySqlDriver())

				if iNumberServer == iAmountConnection:
					strPassiveName = dChangesInfo['passive']

					oPassiveCollection = PymongoCollection(self._oDatabaseMongodb, strPassiveName, False)
					oPassiveCollection.remove()

					iRunSuccessful = 0

					for i in range (0, iNumberServer):
						oResult 		= None
						oConnector		= None

						oConnector  	= self.GetAllMySqlConnection()[i]
						strSQL 			= self.GetAllMySqlDriver()[i].GetQueryByMaintenancesGroups()
						oResult 		= oConnector.QueryAllData(strSQL) #Query Data from mysql server
						iZbxServerId 	= self.GetAllMySqlDriver()[i].GetZabbixServerId()

						if oResult is not None:
							for row in oResult:
								lMaintenanceId	= long(row['maintenanceid'])
								lMaintenanceId 	= long((lMaintenanceId * 256) + iZbxServerId)
								lGroupId		= long(row['groupid'])
								lGroupId		= long((lGroupId * 256) + iZbxServerId)

								dMaintenancesGroupsInfo = dict();
								dMaintenancesGroupsInfo['zbx_groupid'] 		= long(row['groupid'])
								dMaintenancesGroupsInfo['zbx_server_id']	= iZbxServerId

								self._oMongodbController.SaveMaintenancesGroups(oPassiveCollection, lMaintenanceId, lGroupId, dMaintenancesGroupsInfo)

							iRunSuccessful += 1

					if 	iNumberServer == iRunSuccessful:
						self._oMongodbController.SwitchChangesActive(strCollectionname, dChangesInfo['active'], dChangesInfo['passive'])

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*****************************************************************
	# Function: PushHostsInfo
	# Description: push data hosts info from zabbix QT & HL
	#*****************************************************************
	def  PushHostsInfo(self):
		try:
			# Get groups backup in zabbix_changes collection by name
			oHostCollection = PymongoCollection(self._oDatabaseMongodb, "hosts", False)
			arrHostID = []

			iNumberServer 		= len(self.GetAllMySqlConnection())
			iAmountConnection 	= len(self.GetAllMySqlDriver())

			if iNumberServer == iAmountConnection:
				iRunSuccessful = 0

				for i in range (0, iNumberServer):
					oResult 		= None
					oConnector		= None

					oConnector  	= self.GetAllMySqlConnection()[i]
					strSQL 			= self.GetAllMySqlDriver()[i].GetQueryHostsInfo()
					oResult 		= oConnector.QueryAllData(strSQL) #Query Data from mysql server
					iZbxServerId 	= self.GetAllMySqlDriver()[i].GetZabbixServerId()

					if oResult is not None:
						for row in oResult:
							lHostId			= long(row['hostid'])
							lHostId			= long(((lHostId - 10000) * 256) + iZbxServerId)

							arrHostID.append(lHostId)

							dHostsInfo = dict();
							dHostsInfo['zbx_hostid'] 	= long(row['hostid'])
							dHostsInfo['zbx_server_id']	= iZbxServerId
							dHostsInfo['is_deleted']	= 0
							self._oMongodbController.SaveHosts(oHostCollection, lHostId, dHostsInfo)
						iRunSuccessful += 1

				if iNumberServer == iRunSuccessful:
					if oHostCollection is not None and len(arrHostID) > 0:
						oHostCollection.update({'hostid':{'$nin': arrHostID}},
											   {'$set':{'is_deleted': 1}},
											   	upsert=False,
												multi=True)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

	#*****************************************
	# Function: MigrationData
	# Description: Main Migration Data
	#*****************************************
	def MigrationData(self):
		try:
			self.PushMaintenancesInfo()
			self.PushMaintenancesHostsInfo()
			self.CloseAllMySqlConnection()
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

