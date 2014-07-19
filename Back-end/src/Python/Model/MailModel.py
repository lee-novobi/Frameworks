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

class CMailModel(CBaseModel):
	def __init__(self, oConfig):
		try:
			super(CMailModel, self).__init__(oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		
	#****************************************************************************************************#
	def ListWaitingSendMail(self):
		try:
			if self.IsMongoAssistantOK():
				arrMails = self.SelectMongoDB({'status': {'$in': [ITSM_STATUS_INITIALIZE, ITSM_STATUS_FAIL]}}, TBL_SEND_MAIL)
				#arrMails = self.SelectMongoDB({'status': {'$in': [ITSM_STATUS_INITIALIZE, 1]}}, TBL_SEND_MAIL)
				return arrMails
			else:
				strErrorMsg = 'CMailModel Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return []
	
	#****************************************************************************************************#
	def ListUnsavedAttachment(self, strSourceFrom, strSourceId):
		arrAttachments = []
		try:
			if self.IsMongoAssistantOK():
				arrAttachments = self.SelectMongoDB({'is_file_saved': NO, 'source_from': { '$regex': strSourceFrom, '$options': 'i' } 
													, 'source_id': strSourceId}, TBL_EXTERNAL_ATTACHMENTS)
				return arrAttachments
			else:
				strErrorMsg = 'CMailModel Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				return []
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass	
		return []
		
	#****************************************************************************************************#
	def ListSavedAttachment(self, strSourceFrom, strSourceId):
		arrAttachments = []
		try:
			if self.IsMongoAssistantOK():
				arrAttachments = self.SelectMongoDB({'is_file_saved': YES, 'source_from': { '$regex': strSourceFrom, '$options': 'i' } 
													, 'source_id': strSourceId}, TBL_EXTERNAL_ATTACHMENTS)
				return arrAttachments
			else:
				strErrorMsg = 'CMailModel Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				return []
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass	
		return []
		
	#****************************************************************************************************#
	def UpdateMailStatus(self, oMail, dNewStatus):
		bResult = False
		try:
			if self.IsMongoAssistantOK():
				strSourceFrom = oMail['source_from']
				strTicketId = oMail['ticket_id']
				#bResult = self.UpdateMongoDB({'source_from': strSourceFrom, 'ticket_id': strTicketId}, dNewStatus, TBL_SEND_MAIL)
				bResult = self.UpdateMongoDB({'_id': oMail['_id']}, dNewStatus, TBL_SEND_MAIL)
			else:
				strErrorMsg = 'CMailModel Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return bResult