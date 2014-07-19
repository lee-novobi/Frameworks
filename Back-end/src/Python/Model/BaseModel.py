# -*- coding: utf-8 -*-

import os, re, sys
from Config import *
from Utilities import Utilities
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
import MySQLdb
import MySQLdb.cursors

sys.stdout = codecs.getwriter('utf_8')(sys.stdout)
sys.stdin = codecs.getreader('utf_8')(sys.stdin)

class CBaseModel(object):
	def __init__(self, oConfig):
		self.m_oConfig = oConfig
		try:
			self.m_nIsActiveMySQLMonitoringAssistantConnection = True
			self.m_nIsActiveMongoMonitoringAssistantConnection = True
			self.m_nIsActiveMongoMasterConnection = True
			
			self.m_oMySQLMonitoringAssistantConn = None
			self.m_oMongoMonitoringAssistantConn = None
			self.m_oMongoMonitoringAssistantDB   = None
			self.m_oMongoMasterInfoDBConn        = None
			self.m_oMongoMasterInfoDB            = None
			
			self.LoadMonitoringAssistanMongoConnection()
			self.LoadMasterMongoConnection()
			self.LoadMonitoringAssistanMySQLConnection()
			
			
			#self.m_oMongoMasterInfoDBConn = self.m_oMongoMonitoringAssistantConn
			#self.m_oMongoMasterInfoDB     = self.m_oMongoMonitoringAssistantDB

			#Register object model to ConnectorMongodb object
			#self.oConnMongodbMonitoringAssistant.register([ZProducts])
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def LoadMonitoringAssistanMongoConnection(self):
		try:
			self.m_oMongoMonitoringAssistantConn = Connection(self.m_oConfig.MongoMonitorAssistantUri, self.m_oConfig.MongoMonitorAssistantPort)
			self.m_oMongoMonitoringAssistantDB   = PymongoDatabase(self.m_oMongoMonitoringAssistantConn, self.m_oConfig.MongoMonitorAssistantSource)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			self.m_nIsActiveMongoMonitoringAssistantConnection = False
	#****************************************************************************************************
	# MongoDB chua cac thong tin master. Vi du nhu la thong tin chung ve host, zabbix_changes, ...      *
	#****************************************************************************************************
	def LoadMasterMongoConnection(self):
		try:
			self.m_oMongoMasterInfoDBConn = Connection(self.m_oConfig.MongoMasterUri, self.m_oConfig.MongoMasterPort)
			self.m_oMongoMasterInfoDB     = PymongoDatabase(self.m_oMongoMasterInfoDBConn, self.m_oConfig.MongoMasterSource)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			self.m_nIsActiveMongoMasterConnection = False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def LoadMonitoringAssistanMySQLConnection(self):
		try:
			#print "LoadMonitoringAssistanMySQLConnection"
			self.m_oMySQLMonitoringAssistantConn = MySQLdb.connect(
												self.m_oConfig.MySqlMonitorAssistantHost,
												self.m_oConfig.MySqlMonitorAssistantUser,
												self.m_oConfig.MySqlMMonitorAssistantPassword,
												self.m_oConfig.MySqlMonitorAssistantSource,
												cursorclass=MySQLdb.cursors.DictCursor,
												charset='utf8',
												use_unicode=True)
			self.m_oMySQLMonitoringAssistantConn.names = "utf8"
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			self.m_nIsActiveMySQLMonitoringAssistantConnection = False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def GetTableActiveBackupStatus(self, strTableName):
		try:
			if self.m_nIsActiveMongoMasterConnection is True:
				dResult = dict()
				oActiveInfoCollection = PymongoCollection(self.m_oMongoMasterInfoDB, TBL_ACTIVE_COLLECTION_INFO, False)
				dResult = oActiveInfoCollection.find({'name': strTableName}, {'active':1, 'backup':1, '_id':0})[0]
				return dResult
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return None
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def	GetMongoActiveCollection(self, strTableName, **arrArgs):
		try:
			if self.m_nIsActiveMongoMasterConnection is True:
				oActivePyCollection = None
				
				oDB = arrArgs.get("db", self.m_oMongoMonitoringAssistantDB)
				dActiveBackupStatus = self.GetTableActiveBackupStatus(strTableName)
				strActive = dActiveBackupStatus['active']
				oActivePyCollection = PymongoCollection(oDB, strActive, False)
				
				return oActivePyCollection
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return None
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def	SwitchActiveTable(self, strTableName, strActiveName, strBackupName):
		try:
			if self.m_nIsActiveMongoMasterConnection is True:
				oActiveInfoCollection = PymongoCollection(self.m_oMongoMasterInfoDB, TBL_ACTIVE_COLLECTION_INFO, False)
				if oActiveInfoCollection.find({'name': strTableName}).count() == 1:
					oActiveInfoCollection.update({ 'name': strTableName },
													  { '$set': {'active': strBackupName, 'backup': strActiveName} })
					return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def IsMySQLOK(self, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMySQLMonitoringAssistantConn)
			if oDB is not None and oDB.open:
				return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def IsMongoAssistantOK(self, **arrArgs):
		return self.m_nIsActiveMongoMonitoringAssistantConnection
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def InsertMySQLDB(self, arrData, strTableName, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMySQLMonitoringAssistantConn)
			if oDB is not None and oDB.open:
				arrSetField = []
				arrSetValue = []
				for key, value in arrData.iteritems():
					arrSetField.append(key)
					if type(value) != dict:
						value = oDB.escape_string(unicode(value).encode('utf8'))
						value = value.decode('utf8')
						value = u''.join([u"'", value, u"'"])
						arrSetValue.append(value)
					else:
						if value['type'] == MYSQL_VALUE_TYPE_EXPRESSION:
							arrSetValue.append(value['value'])
				
				if len(arrSetField) > 0:
					strQuery = u"INSERT INTO %s (%s) VALUES(%s)" % (strTableName, u",".join(arrSetField), u",".join(arrSetValue))
					oDBCursor = oDB.cursor()
					oDBCursor.execute(strQuery)
					Utilities.WriteLog(strQuery, self.m_oConfig.MySqlMonitorAssistantInsertLogPath)
					oDBCursor.close()
			return oDB.insert_id()
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return 0
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateMySQLDB(self, arrCondition, arrData, strTableName, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMySQLMonitoringAssistantConn)
			if oDB is not None and oDB.open:
				arrSet = []
				arrKey = []
				for key, value in arrData.iteritems():
					if type(value) != dict:
						value  = oDB.escape_string(unicode(value).encode('utf8'))
						value  = value.decode('utf8')
						strSet = u''.join([key, u"=", u"'", value, u"'"])
						
						#arrSetValue.append(value)
						arrSet.append(strSet)
					else:
						if value['type'] == MYSQL_VALUE_TYPE_EXPRESSION:
							strSet = u''.join([key, u"=", value['value']])
							arrSet.append(strSet)

				if len(arrSet) > 0:
					strQuery = u"UPDATE %s SET " % (strTableName)
					strQuery = unicode(strQuery) + u",".join(arrSet)
					
					if len(arrCondition) > 0:
						for key, value in arrCondition.iteritems():
							if type(value) != dict:
								value  = oDB.escape_string(unicode(value).encode('utf8'))
								value  = value.decode('utf8')
								strSet = u''.join([key, u"=", u"'", value, u"'"])
							else:
								if value['type'] == MYSQL_VALUE_TYPE_EXPRESSION:
									strSet = u''.join([key, u"=", value['value']])
							arrKey.append(strSet)
						
						strQuery = strQuery + u" WHERE " + u" AND ".join(arrKey)
						oDBCursor = oDB.cursor()
						oDBCursor.execute(strQuery)
						Utilities.WriteLog(strQuery, self.m_oConfig.MySqlMonitorAssistantUpdateLogPath)
						oDBCursor.close()
			return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
	
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def SelectMongoDB(self, arrCondition, strTableName, **arrArgs):
		arrResult = []
		try:
			oDB = arrArgs.get("db", self.m_oMongoMonitoringAssistantDB)
			if oDB is not None:
				oCollection = PymongoCollection(oDB, strTableName, False)
				if oCollection is not None:
					oCursor = oCollection.find(arrCondition)
					if oCursor is not None:
						for oDoc in oCursor:
							arrResult.append(oDoc)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return arrResult
		
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def SelectOneMongoDB(self, arrCondition, strTableName, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMongoMonitoringAssistantDB)
			if oDB is not None:
				oCollection = PymongoCollection(oDB, strTableName, False)
				if oCollection is not None:
					return oCollection.find_one(arrCondition)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return None
	
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateMongoDB(self, arrCondition, arrData, strTableName, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMongoMonitoringAssistantDB)
			if oDB is not None:
				oCollection = PymongoCollection(oDB, strTableName, False)
				if oCollection is not None:
					oCollection.update(arrCondition, {'$set':arrData})
					return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
		
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpsertMongoDB(self, arrCondition, arrData, strTableName, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMongoMonitoringAssistantDB)
			if oDB is not None:
				oCollection = PymongoCollection(oDB, strTableName, False)
				if oCollection is not None:
					oCollection.update(arrCondition, {'$set':arrData}, upsert=True)
					return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
	
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def InsertMongoDB(self, arrData, strTableName, **arrArgs):
		try:
			oDB = arrArgs.get("db", self.m_oMongoMonitoringAssistantDB)
			if oDB is not None:
				oCollection = PymongoCollection(oDB, strTableName, False)
				if oCollection is not None:
					oCollection.insert(arrData)
					return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def CloseMySQLConnection(self):
		try:
			#print "CloseMySQLConnection"
			if self.m_oMySQLMonitoringAssistantConn is not None:
				self.m_oMySQLMonitoringAssistantConn.close()
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass				
