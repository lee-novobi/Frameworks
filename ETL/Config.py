#Description: Load configuration file for all module
import ConfigParser
import os
from MySqlDriver import CMySqlDriver
from Utility import Utilities
from inspect import stack

#Class Config
#Load all parameter in configuration file and return value for each parameter
class CConfig:

	def __init__(self):
		self.m_arrMySqlDriver = []
		self.LoadConfig()

	def	 LoadConfig(self):
		try:
			oConfig = ConfigParser.ConfigParser()
			fnConfig = os.path.join(os.path.dirname(__file__), 'Config.ini')
			oConfig.read(fnConfig)
			self.LoadParameterConfig(oConfig)
			self.LoadDataBaseConfig(oConfig)
			self.LoadMongoDBConfig(oConfig)
			self.LoadZabbixTrapper(oConfig)

		except Exception, exc:
			strErrorMsg = 'Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def  AddDatabaseConfig(self, oConfig, strDatabase):
		try:
			#Add Connection Config
			strDBHost			= oConfig.get(strDatabase, 'Host', 'localhost')
			strDBUser			= oConfig.get(strDatabase, 'User', 'root')
			strDBPassword		= oConfig.get(strDatabase, 'Password', 'sa')
			strDBSource			= oConfig.get(strDatabase, 'Source', '')
			strDBHostAlias		= oConfig.get(strDatabase, 'Alias', 'localhost')
			iDBPeriod			= int(oConfig.get(strDatabase, 'Period', '0'))
			iZabbixServerId		= int(oConfig.get(strDatabase, 'zabbix_server_id', '1'))
			strZabbixServerName = oConfig.get(strDatabase, 'zabbix_server_name', 'local')
			strZabbixVersion	= oConfig.get(strDatabase, 'zabbix_version', '')
			oMysqlDriver			= CMySqlDriver(strDBHost, strDBUser, strDBPassword, strDBSource, strDBHostAlias, iDBPeriod, iZabbixServerId, strZabbixServerName, strZabbixVersion)
			self.m_arrMySqlDriver.append(oMysqlDriver)
		except Exception, exc:
			strErrorMsg = '%s Connection Error: %s' % (strDatabase, str(exc)) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def	 LoadDataBaseConfig(self, oConfig):
		self.m_iAmountConnection = 0
		self.AddDatabaseConfig(oConfig, 'FIRST_DATABASE')
		self.m_iAmountConnection += 1
		self.AddDatabaseConfig(oConfig, 'SECOND_DATABASE')
		self.m_iAmountConnection += 1


	def	 LoadMongoDBConfig(self, oConfig):
		self.m_strMongoDBHost		= oConfig.get('MA_MONGODB', 'Host', 'localhost')
		self.m_strMongoDBUser		= oConfig.get('MA_MONGODB', 'User', 'admin')
		self.m_iMongoDBPort			= int(oConfig.get('MA_MONGODB', 'Port', '27017'))
		self.m_strMongoDBPassword	= oConfig.get('MA_MONGODB', 'Password', '')
		self.m_strMongoDBSource		= oConfig.get('MA_MONGODB', 'Source', '')
		self.m_strMongoDBUri		= "mongodb://" + self.m_strMongoDBUser + ':' + self.m_strMongoDBPassword
		self.m_strMongoDBUri	   +=	"@" + self.m_strMongoDBHost + '/' + self.m_strMongoDBSource

	def LoadParameterConfig(self, oConfig):
		self._strLogParameter	= oConfig.get('PARAMETER', 'Error_Log', '')

	def  LoadZabbixTrapper(self, oConfig):
		self.m_strHostTrapper  		= oConfig.get('TRAPPER', 'HostTrapper', 'localhost')
		self.m_strLocationTrapper	= oConfig.get('TRAPPER', 'LocationTrapper', '')
		self.m_strZabbixServer		= oConfig.get('TRAPPER', 'ZabbixServer', '127.0.0.1')

	def  GetAmountConnection(self):
		return self.m_iAmountConnection

	def	 GetArrayMySqlDriver(self):
		return self.m_arrMySqlDriver

	def  DestroyArrayMysqlDriver(self):
		self.m_arrMySqlDriver = []

	def	 GetNumberOfConnection(self):
		return len(self.m_arrMySqlDriver)

	def	 GetMongoDBHost(self):
		return self.m_strMongoDBHost

	def	 GetMongoDBUser(self):
		return self.m_strMongoDBUser

	def	 GetMongoDBPassword(self):
		return self.m_strMongoDBPassword

	def	 GetMongoDBPort(self):
		return self.m_iMongoDBPort

	def	 GetMongoDBSource(self):
		return self.m_strMongoDBSource

	def	 GetMongoDBUri(self):
		return self.m_strMongoDBUri

	def	 GetLogParameter(self):
		return self._strLogParameter

	def GetHostTrapper(self):
		return self.m_strHostTrapper

	def GetLocationTrapper(self):
		return self.m_strLocationTrapper

	def GetZabbixServer(self):
		return self.m_strZabbixServer

	def	 CanStopService(self):
		oConfig = ConfigParser.ConfigParser()
		fnConfig = os.path.join(os.path.dirname(__file__), 'config.ini')
		oConfig.read(fnConfig)
		return oConfig.get('REALTIME', 'Stop', 0)


