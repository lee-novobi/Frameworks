# -*- coding: utf-8 -*-
# encoding: utf-8
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
from mongokit import ObjectId
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
import MySQLdb
import MySQLdb.cursors

sys.stdout = codecs.getwriter('utf_8')(sys.stdout)
sys.stdin = codecs.getreader('utf_8')(sys.stdin)

class CPromotionAlertModel(CBaseModel):
	def __init__(self, oConfig):
		try:
			super(CPromotionAlertModel, self).__init__(oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def WriteRawAlert(self, oNewRawAlert, oOldRawAlert):
		try:
			if oOldRawAlert is not None:
				self.UpdateRawAlert(oNewRawAlert)
			else:
				self.InsertRawAlert(oNewRawAlert)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def IsRawAlertExisted(self, oAlert):
		bResult = False
		try:
			if self.IsMongoAssistantOK():
				#strAlertKey = unicode(oAlert['ProgramID']) + u"_" + unicode(oAlert['API'])
				#strAlertKey = (oAlert['ProgramID']) + "_" + (oAlert['API'])
				strAlertKey = oAlert['ProgramID']
				oOldAlert = self.SelectOneMongoDB({'ProgramID': strAlertKey, 'deleted':0}, TBL_ALERT_PROMOTION)
				bResult = oOldAlert is not None
			else:
				strErrorMsg = 'CPromotionAlertModel-->IsAlertExisted Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return bResult
		
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def IsCentralAlertExisted(self, oRawAlert):
		bResult = False
		try:
			if self.IsMongoAssistantOK():
				#strAlertKey = unicode(oRawAlert['ProgramID']) + u"_" + unicode(oRawAlert['API'])
				#strAlertKey = (oRawAlert['ProgramID']) + "_" + (oRawAlert['API'])
				strAlertKey = oRawAlert['ProgramID']
				oOldCentralAlert = self.SelectOneMongoDB({'ticket_id': strAlertKey, 'is_show': CENTRAL_ALERT_STATUS_SHOW, 'source_from': {'$regex': INCIDENT_SRC_FROM_PROMOTION}}, TBL_CENTRAL_ALERT)
				bResult = oOldCentralAlert is not None
			else:
				strErrorMsg = 'CPromotionAlertModel-->IsCentralAlertExisted Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return bResult
	
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def UpdateRawAlert(self, oNewRawAlert):
		bResult = False
		try:
			if self.IsMongoAssistantOK():
				#strAlertKey = unicode(oNewRawAlert['ProgramID']) + u"_" + unicode(oNewRawAlert['API'])
				#strAlertKey = (oNewRawAlert['ProgramID']) + "_" + (oNewRawAlert['API'])
				strAlertKey = oNewRawAlert['ProgramID']
				bResult = self.UpdateMongoDB({'ProgramID': strAlertKey, 'deleted': 0}, oNewRawAlert, TBL_ALERT_PROMOTION)
			else:
				strErrorMsg = 'CPromotionAlertModel-->UpdateAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return bResult
	
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def InsertRawAlert(self, oNewRawAlert):
		bResult = False
		try:
			if self.IsMongoAssistantOK():
				oNewRawAlert['deleted'] = 0
				bResult = self.InsertMongoDB(oNewRawAlert, TBL_ALERT_PROMOTION)
			else:
				strErrorMsg = 'CPromotionAlertModel-->InsertAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return bResult	
	
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def TurnOffRawAlert(self, arrProcessingAlert):
		bResult = False
		try:
			if self.IsMongoAssistantOK():
				strTime = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
				bResult = self.UpdateMongoDB({'ProgramID':{'$nin':arrProcessingAlert}, 'deleted': 0}, {'deleted':1, 'deleted_date': strTime}, TBL_ALERT_PROMOTION)
			else:
				strErrorMsg = 'CPromotionAlertModel-->TurnOffAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return bResult	
	
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def WriteCentralAlert(self, oNewRawAlert, oOldRawAlert):
		try:
			if self.IsCentralAlertExisted(oNewRawAlert):
				self.UpdateCentralAlert(oNewRawAlert, oOldRawAlert)
			else:
				self.InsertCentralAlert(oNewRawAlert)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass

	#****************************************************************************************************
	#
	#****************************************************************************************************
	def UpdateCentralAlert(self, oNewRawAlert, oOldRawAlert):
		try:
			if self.IsMongoAssistantOK():
				if oOldRawAlert is not None:
					#strAlertKey = unicode(oNewRawAlert['ProgramID']) + u"_" + unicode(oNewRawAlert['API'])
					#strAlertKey = (oNewRawAlert['ProgramID']) + "_" + (oNewRawAlert['API'])
					strAlertKey = (oNewRawAlert['ProgramID'])
					oOldCentralAlert = self.SelectOneMongoDB({'ticket_id': strAlertKey, 'is_show': CENTRAL_ALERT_STATUS_SHOW, 'source_from': {'$regex': INCIDENT_SRC_FROM_PROMOTION}}, TBL_CENTRAL_ALERT)

					if self.CompareRawAlert(oNewRawAlert, oOldRawAlert) is not True:
						self.InsertCentralAlert(oNewRawAlert)
						if oOldCentralAlert is not None:
							self.TurnOffReplacedCentralAlert(oOldCentralAlert)
							self.CopyACKToNewestAlert(oOldCentralAlert)
			else:
				strErrorMsg = 'CPromotionAlertModel-->UpdateCentralAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass

	#****************************************************************************************************
	#
	#****************************************************************************************************
	def InsertCentralAlert(self, oAlert):
		try:
			if self.IsMongoAssistantOK():
				#strAlertKey = unicode(oAlert['ProgramID']) + u"_" + unicode(oAlert['API'])
				#strAlertKey = (oAlert['ProgramID']) + "_" + (oAlert['API'])
				strAlertKey = (oAlert['ProgramID'])
				print strAlertKey
				oRawAlert = self.SelectOneMongoDB({'ProgramID': strAlertKey, 'deleted': 0}, TBL_ALERT_PROMOTION)
				
				strDescription = u""
				for key,value in oRawAlert.iteritems():
					try:
						if key != u"_id":
							strDescription = strDescription + key + ": "
							if not isinstance(value, unicode):
								value = unicode(value)
							
							strDescription = strDescription + value + "\n"
					except Exception, exc:
						strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
						Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
						pass
				
				if not 'Case' in oRawAlert:
					oRawAlert['Case'] = 0

				oCentralAlert = dict()
				oCentralAlert['ticket_id']     = strAlertKey
				oCentralAlert['source_from']   = INCIDENT_SRC_FROM_PROMOTION
				oCentralAlert['source_id']     = str(oRawAlert['_id'])
				oCentralAlert['title']         = u"[Game: %s][Event: %s][CASE:%s][API: %s][SE:%s, %s]" % (oRawAlert['Game'], oRawAlert['Program'], oRawAlert['Case'], oRawAlert['API'], oRawAlert['Author'], oRawAlert['PhoneAuthor'])
				oCentralAlert['description']   = strDescription
				oCentralAlert['alert_message'] = u"[Game: %s][Event: %s][CASE:%s][API: %s][SE:%s, %s]" % (oRawAlert['Game'], oRawAlert['Program'], oRawAlert['Case'], oRawAlert['API'], oRawAlert['Author'], oRawAlert['PhoneAuthor'])
				oCentralAlert['num_of_case']   = oRawAlert['Case']
				oCentralAlert['is_acked']      = 0
				oCentralAlert['is_show']       = 1
				oCentralAlert['product']       = oRawAlert['Game']
				oCentralAlert['create_date']   = int(time.time())
				oCentralAlert['clock']         = int(time.time())
				
				self.InsertMongoDB(oCentralAlert, TBL_CENTRAL_ALERT)
			else:
				strErrorMsg = 'CPromotionAlertModel-->InsertCentralAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass

	#****************************************************************************************************
	#
	#****************************************************************************************************
	def CompareRawAlert(self, oAlertNew, oAlertOld):
		bResult = True
		try:
			for strAttr, oValue in oAlertNew.items():
				#print u"Compare %s:[Old:%s][New:%s]" % (strAttr, str(oAlertOld[strAttr]).decode("utf-8"), str(oValue).decode("utf-8"))
				#if isinstance(oValue, int):
				#	print u"Compare %s:[Old:%s][New:%s]" % (strAttr, unicode(oAlertOld[strAttr], errors="ignore"), unicode(oValue, errors="ignore"))
				#print type(oValue)
				if strAttr in oAlertOld:
					if oAlertOld[strAttr] != oValue:
						bResult = False
						break
				else:
					bResult = False
					break
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		#print bResult
		return bResult
			
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def TurnOffReplacedCentralAlert(self, oCentralAlert):
		try:
			if self.IsMongoAssistantOK():
				#print "Update %s" % oCentralAlert[u'_id']
				self.UpdateMongoDB({'_id': oCentralAlert['_id']}, {'is_show': CENTRAL_ALERT_STATUS_REPLACED}, TBL_CENTRAL_ALERT)
			else:
				strErrorMsg = 'CPromotionAlertModel-->TurnOffReplacedCentralAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def CopyACKToNewestAlert(self, oSourceCentralAlert):
		try:
			if self.IsMongoAssistantOK():
				oNewestAlert = self.SelectOneMongoDB({'ticket_id': oSourceCentralAlert['ticket_id'], 'is_show': CENTRAL_ALERT_STATUS_SHOW, 'source_from': {'$regex': INCIDENT_SRC_FROM_PROMOTION}}, TBL_CENTRAL_ALERT)
				if oNewestAlert is not None:
					arrACK = self.SelectMongoDB({'alert_id': oSourceCentralAlert['_id']}, TBL_ALERT_ACK)
					for oACK in arrACK:
						oACK.pop("_id", None)
						oACK['alert_id'] = oNewestAlert['_id']
						self.InsertMongoDB(oACK, TBL_ALERT_ACK)
			else:
				strErrorMsg = 'CPromotionAlertModel-->CopyAlertACK Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
	#****************************************************************************************************
	#
	#****************************************************************************************************
	def TurnOffCentralAlert(self, arrProcessingAlert):
		try:
			if self.IsMongoAssistantOK():
				self.UpdateMongoDB({'ticket_id':{'$nin':arrProcessingAlert}, 'is_show': CENTRAL_ALERT_STATUS_SHOW, 'source_from': {'$regex': INCIDENT_SRC_FROM_PROMOTION}}, {'is_show': CENTRAL_ALERT_STATUS_OFF}, TBL_CENTRAL_ALERT)
			else:
				strErrorMsg = 'CPromotionAlertModel-->TurnOffCentralAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	
	#****************************************************************************************************
	#
	#****************************************************************************************************		
	def LoadRawAlert(self, strAlertKey):
		try:
			if self.IsMongoAssistantOK():
				oRawAlert = self.SelectOneMongoDB({'ProgramID': strAlertKey, 'deleted': 0}, TBL_ALERT_PROMOTION)
				return oRawAlert
			else:
				strErrorMsg = 'CPromotionAlertModel-->LoadRawAlert Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return None