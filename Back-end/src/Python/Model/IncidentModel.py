import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))

from inspect import stack
import re
from datetime import datetime
from BaseModel import *
from Constants import *
from Config import *
from Utilities import Utilities
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
import MySQLdb
import MySQLdb.cursors

class CIncidentModel(CBaseModel):
	def __init__(self, oConfig):
		try:
			super(CIncidentModel, self).__init__(oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def ListWaitingOpenIncident(self):
		try:
			if self.IsMySQLOK():
				oDBCursor = self.m_oMySQLMonitoringAssistantConn.cursor()
				if oDBCursor is not None:
					strSQL = """SELECT id AS sdk_id,product_alias AS service,
											IFNULL(department_code, "") AS department,

											assignment_group AS assignmentGroup,
											'incident' AS category,
											title AS title,
											IFNULL(area,"") AS area,
											IFNULL(subarea,"") AS subArea,
											IFNULL(description,"") AS description,
											CAST(impact_level AS CHAR(2)) AS impact,
											CAST(urgency_level AS CHAR(2)) AS urgency,
											IFNULL(assignee,"") AS assignee,
											DATE_FORMAT(downtime_start, '%s') AS downstart,
											DATE_FORMAT(outage_start, '%s') AS outagestart,
											DATE_FORMAT(downtime_start, '%s') AS mysql_downstart,
											DATE_FORMAT(outage_start, '%s') AS mysql_outagestart,
											IFNULL(critical_asset, "") AS criticalasset,
											IFNULL(location,"") AS location,
											caused_by_external AS causedbyexternalservice,
											IFNULL(caused_by_external_dept,"") AS causedbydept,
											IFNULL(related_id,"") AS relateid,
											IFNULL(related_id_change,"") AS relateidchange,
											IFNULL(sdknote,"") AS sdknote,
											IFNULL(detector,"") AS detector,
											IFNULL(created_by,"") AS createdBy,
											IFNULL(attachments, "") AS attachment,
											IFNULL(link, "") AS link,
											IFNULL(is_downtime, "false") AS isdowntime,
											
											IFNULL(ccutime, "") AS ccutime,
											IFNULL(user_impacted, "") AS connection,
											IFNULL(rootcause_category, "") AS causecategory,
											IFNULL(customer_case, "") AS customerimpacted,
											IFNULL(affected_ci, "") AS affactedCI, 
											auto_update_impact_level,
											IFNULL(kb_id, "") AS kb_id,
											IFNULL(bug_category, "") AS bug_category,
											IFNULL(unit, "") AS unit,
											src_from AS source_from,
											src_id AS source_id 
									FROM %s WHERE `status` IN (%s)""" % (MYSQL_DATE_FORMAT_ITSM, MYSQL_DATE_FORMAT_ITSM, MYSQL_DATE_FORMAT_MYSQL, MYSQL_DATE_FORMAT_MYSQL, TBL_INCIDENT_WAITING_OPEN, LIST_STATUS_INCIDENT_WAITING_OPEN)
					#print strSQL
					oDBCursor.execute(strSQL)
					return oDBCursor.fetchall()
			else:
				strErrorMsg = 'ListWaitingOpenIncident Error: Disconnected from MySQL.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return []
		
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def ListWaitingUpdateIncident(self):
		try:
			if self.IsMySQLOK():
				oDBCursor = self.m_oMySQLMonitoringAssistantConn.cursor()
				if oDBCursor is not None:
				# area AS area,
				# subarea AS subArea,
					strSQL = """SELECT 
								id as sdk_id,
								itsm_incident_id as id,
								status as incident_status,
								assignment_group AS assignmentGroup,
								'incident' AS category,
								title,
								description AS description,
								area AS area,
								subarea AS subArea,
								DATE_FORMAT(downtime_start, '%s') AS downstart,
								product_alias AS service,
								DATE_FORMAT(outage_start, '%s') AS outagestart,
								DATE_FORMAT(outage_end, '%s') AS outageend,
								department_code as department,
								critical_asset as criticalasset,
								created_by AS createdBy,
								attachments AS attachment,
								CAST(impact_level AS CHAR(2)) AS impact,
								CAST(urgency_level AS CHAR(2)) AS urgency,
								assignee AS assignee,
								IFNULL(location,"") AS location,
								caused_by_external_dept AS causedbydept,
								caused_by_external AS causedbyexternalservice,
								related_id AS relateid,
								related_id_change AS relateidchange,
								is_downtime AS isdowntime,
								sdknote AS sdknote,
								detector AS detector,
								ccutime AS ccutime,
								user_impacted AS connection,
								rootcause_category AS causecategory,
								customer_case AS customerimpacted,
								affected_ci AS affactedCI, 
								resolved_by AS resolvedby,	
								solution,
								closurecode AS closureCode,
								'update ------' AS Jupdate,
								IFNULL(kb_id, "") AS kb_id,
								IFNULL(bug_category, "") AS bug_category,
								IFNULL(unit, "") AS unit								
								FROM %s WHERE `sdk_update_to_itsm_count` < %s AND `sdk_update_to_itsm_status` IN (%s)
								ORDER BY itsm_incident_id ASC, id ASC""" % (MYSQL_DATE_FORMAT_ITSM
																		, MYSQL_DATE_FORMAT_ITSM
																		, MYSQL_DATE_FORMAT_ITSM
																		, TBL_INCIDENT_WAITING_UPDATE
																		, ITSM_RETRY_TIMES
																		, LIST_STATUS_INCIDENT_WAITING_UPDATE)
					#print strSQL
					oDBCursor.execute(strSQL)
					return oDBCursor.fetchall()
			else:
				strErrorMsg = 'ListWaitingOpenIncident Error: Disconnected from MySQL.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return []
		
	#****************************************************************************************************
	def GetIncidentByITSMId(self, strITSMId):
		try:
			if self.IsMySQLOK():
				oDBCursor = self.m_oMySQLMonitoringAssistantConn.cursor()
				if oDBCursor is not None:
					strSQL = """SELECT * FROM %s WHERE incident_itsm_id = '%s'""" % (TBL_INCIDENT_FOLLOW, strITSMId)
					oDBCursor.execute(strSQL)
					return oDBCursor.fetchall()
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
				
		return False
		
	#****************************************************************************************************
	def DeleteIncidentUpdateSuccess(self):
		try:
			if self.IsMySQLOK():
				oDBCursor = self.m_oMySQLMonitoringAssistantConn.cursor()
				if oDBCursor is not None:
					strSQL = """DELETE FROM %s WHERE sdk_update_to_itsm_status = %s""" % (TBL_INCIDENT_WAITING_UPDATE, STATUS_INCIDENT_UPDATE_SUCCESS)
					#print strSQL
					oDBCursor.execute(strSQL)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
				
		return False
		
	#****************************************************************************************************
	def InsertIncidentTrackingChanges(self, dChangedData):
		try:
			if self.IsMySQLOK():
				self.InsertMySQLDB(dChangedData, TBL_INCIDENT_TRACKING_CHANGES)
				return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
				
		return False
		
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateWaitingUpdateIncident(self, oCondition, dData):
		try:
			if self.IsMySQLOK():
				self.UpdateMySQLDB(oCondition, dData, TBL_INCIDENT_WAITING_UPDATE)
				return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
		
		
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def InsertIncidentFollow(self, oIncident):
		try:
			if self.IsMySQLOK():
				self.InsertMySQLDB(oIncident, TBL_INCIDENT_FOLLOW)
				return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
				
		return False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateIncidentWaiting(self, oCondition, oIncident):
		try:
			if self.IsMySQLOK():
				self.UpdateMySQLDB(oCondition, oIncident, TBL_INCIDENT_WAITING_OPEN)
				return True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
				
		return False
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def ListIncident2NotifyStatus(self, strSourceFrom=''):
		try:
			if self.IsMongoAssistantOK():
				strSourceFrom = strSourceFrom.lower()
				if strSourceFrom in SRC_FROM_TABLE_MAP:
					strTable = SRC_FROM_TABLE_MAP[strSourceFrom]
					return self.SelectMongoDB({'itsm_status_notified': NO}, strTable)
				else:
					strErrorMsg = 'ListIncident2NotifyStatus Error: Source alert table "%s" not found.' % (strSourceFrom)
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			else:
				strErrorMsg = 'ListIncident2NotifyStatus Error: Disconnected from MySQL.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return []
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def LoadExternalRawAlert(self, oCondition, strSourceFrom):
		try:
			strSourceFrom = strSourceFrom.lower()
			if strSourceFrom in SRC_FROM_TABLE_MAP:
				if self.m_nIsActiveMongoMonitoringAssistantConnection:
					strTable = SRC_FROM_TABLE_MAP[strSourceFrom]
					return self.SelectMongoDB(oCondition, strTable)
				else:
					strErrorMsg = 'LoadExternalRawAlert Error: Disconnected from MongoDB.'
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			else:
				strErrorMsg = 'LoadExternalRawAlert Error: Source alert table "%s" not found.' % (strSourceFrom)
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return None
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateExternalRawAlert(self, oCondition, oAlertInfo, strSourceFrom):
		try:
			strSourceFrom = strSourceFrom.lower()
			if strSourceFrom in SRC_FROM_TABLE_MAP:
				if self.m_nIsActiveMongoMonitoringAssistantConnection:
					strTable = SRC_FROM_TABLE_MAP[strSourceFrom]
					self.UpdateMongoDB(oCondition, oAlertInfo, strTable)
				else:
					strErrorMsg = 'UpdateExternalRawAlert Error: Disconnected from MongoDB.'
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			else:
				strErrorMsg = 'UpdateExternalRawAlert Error: Source alert table "%s" not found.' % (strSourceFrom)
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateCentralAlert(self, oCondition, oAlertInfo):
		try:
			if self.m_nIsActiveMongoMonitoringAssistantConnection:
				self.UpdateMongoDB(oCondition, oAlertInfo, TBL_CENTRAL_ALERT)
			else:
				strErrorMsg = 'UpdateCentralAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def UpdateStatusOfExternalRawAlert(self, nAlertId, nStatusId, strSourceFrom):
		try:
			strSourceFrom = strSourceFrom.lower()
			if strSourceFrom in SRC_FROM_TABLE_MAP:
				if self.m_nIsActiveMongoMonitoringAssistantConnection:
					strTable = SRC_FROM_TABLE_MAP[strSourceFrom]
					
					oRs = self.SelectMongoDB({'id': int(nAlertId)}, strTable)
					if oRs is not None:
						print oRs
				else:
					strErrorMsg = 'UpdateStatusOfExternalRawAlert Error: Disconnected from MongoDB.'
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			else:
				strErrorMsg = 'UpdateStatusOfExternalRawAlert Error: Source alert table "%s" not found.' % (strSourceFrom)
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	#****************************************************************************************************
	#End Div: #Zabbix_Changes                                                                           *
	#****************************************************************************************************
	def InsertMailingList(self, oSendMailInfo):
		try:
			if self.m_nIsActiveMongoMonitoringAssistantConnection:
				self.InsertMongoDB(oSendMailInfo, TBL_SEND_MAIL)
				return True
			else:
				strErrorMsg = 'InsertMailingList Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
		return False
		
	#****************************************************************************************************
	#purpose: find old alert_message in MA alerts collection by source_id								*
	#		to attach IncidentPrefix to become to new alert_message										*
	#****************************************************************************************************
	#thaodt signed
	def GetAlertMessageFromCentralAlertCollection(self, oCondition):
		try:
			if self.m_nIsActiveMongoMonitoringAssistantConnection:
				return self.SelectMongoDB(oCondition, TBL_CENTRAL_ALERT)
			else:
				strErrorMsg = 'GetAlertMessageFromCentralAlertCollection Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return None