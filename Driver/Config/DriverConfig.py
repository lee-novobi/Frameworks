import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../CMDBAudit'))
#Description: Load configuration file for all module
import ConfigParser
from inspect import stack
from Utilities import Utilities
from Constants import *
#from MysqlDriver import MySqlConnection

#Class Config
#Load all parameter in configuration file and return value for each parameter
class Config(object):
	def __init__(self):
		try:
			self.m_oConfig = ConfigParser.ConfigParser()
			fnConfig = os.path.join(os.path.dirname(__file__), 'Config.ini')
			self.m_oConfig.read(fnConfig)
			self.LoadErrorLogConfig(self.m_oConfig)
			self.LoadMongoMADBConfig(self.m_oConfig)

		except Exception, exc:
			strErrorMsg = 'Init Config Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def  LoadErrorLogConfig(self, oConfig):
		self._strErrorLog = oConfig.get('ERROR', 'ErrorLog', '')

	def  LoadScheduleConfig(self, oConfig):
		self._iSchedule	= int(oConfig.get('REALTIME', 'Schedule', ''))

	def	 LoadMongoMADBConfig(self, oConfig):
		self._strMongoMADBHost		= oConfig.get('MONGODB_MA', 'Host', 'localhost')
		self._strMongoMADBUser		= oConfig.get('MONGODB_MA', 'User', 'admin')
		self._iMongoMADBPort			= int(oConfig.get('MONGODB_MA', 'Port', '27017'))
		self._strMongoMADBPassword	= oConfig.get('MONGODB_MA', 'Password', '')
		self._strMongoMADBSource		= oConfig.get('MONGODB_MA', 'Source', '')
		self._strMongoMADBUri			= "mongodb://" + self._strMongoMADBUser + ':' + self._strMongoMADBPassword
		self._strMongoMADBUri			+=	"@" + self._strMongoMADBHost + '/' + self._strMongoMADBSource

	def	 GetErrorLog(self):
		return self._strErrorLog

	def  GetSchedule(self):
		return self._iSchedule

	def	 GetMongoMADBHost(self):
		return self._strMongoMADBHost

	def	 GetMongoMADBUser(self):
		return self._strMongoMADBUser

	def	 GetMongoMADBPassword(self):
		return self._strMongoMADBPassword

	def	 GetMongoMADBPort(self):
		return self._iMongoMADBPort

	def	 GetMongoMADBSource(self):
		return self._strMongoMADBSource

	def	 GetMongoMADBUri(self):
		return self._strMongoMADBUri

	def	 CanStopService(self):
		oConfig = ConfigParser.ConfigParser()
		fnConfig = os.path.join(os.path.dirname(__file__), 'Config.ini')
		oConfig.read(fnConfig)
		return oConfig.get('REALTIME', 'Stop', 0)
