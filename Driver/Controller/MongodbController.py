import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))

from inspect import stack
from Constants import *
import DriverConfig
import re
from Utilities import Utilities
from datetime import datetime
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase

class MongodbController(object):
	def __init__(self):
		# Create connector mysql object
		self.oConfig = DriverConfig.Config()
		self.m_nIsActiveConnection = True
		try:
			# Create connector mongokit object
			self.oConnectorMongoMAdb	    = Connection(self.oConfig.GetMongoMADBUri(), self.oConfig.GetMongoMADBPort())
			self.oDatabaseMongoMAdb			= PymongoDatabase(self.oConnectorMongoMAdb, self.oConfig.GetMongoMADBSource())

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.oConfig)
			self.m_nIsActiveConnection = False

	@property
	def IsActiveConnection(self):
		return self.m_nIsActiveConnection
