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

class CG8AlertModel(CBaseModel):
	def __init__(self, oConfig):
		try:
			super(CG8AlertModel, self).__init__(oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		
	#****************************************************************************************************#
	def GetG8AlertByTicketId(self, strTicketId):
		try:
			if self.IsMongoAssistantOK():
				arrAlerts = self.SelectMongoDB({'ticket_id': strTicketId}, TBL_ALERT_G8)
				if len(arrAlerts) > 0:
					return arrAlerts[0]
			else:
				strErrorMsg = 'CG8AlertModel Error: Disconnected from MongoDB.'
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
			
		return None
