import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
#Description: Load configuration file for all module
import ConfigParser
from inspect import stack
from Utilities import *
from Constants import *
#from MysqlDriver import MySqlConnection

#Class Config
#Load all parameter in configuration file and return value for each parameter
class CConfig(object):
	def __init__(self):
		try:
			self.m_oConfig = ConfigParser.ConfigParser()
			fnConfig = os.path.join(os.path.dirname(__file__), CONFIG_FILENAME)
			self.m_oConfig.read(fnConfig)
			self.LoadErrorLogConfig(self.m_oConfig)
			
			self.LoadITSMAPIConfig(self.m_oConfig)
			self.LoadG8APIConfig(self.m_oConfig)
			self.LoadCSAPIConfig(self.m_oConfig)
			self.LoadPromotionAPIConfig(self.m_oConfig)
			
			self.LoadMongoMonitorAssistantConfig(self.m_oConfig)
			self.LoadMongoMasterConfig(self.m_oConfig)
			self.LoadMySqlMonitoringAssistantConfig(self.m_oConfig)
			
			self.LoadMailServerConfig(self.m_oConfig)
			self.LoadG8MailSenderConfig(self.m_oConfig)

		except Exception, exc:
			strErrorMsg = 'Init Config Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)
			
	# -------------------------------------------------------------------
	def LoadMailServerConfig(self, oConfig):
		self._strMailServerHost = oConfig.get(MAIL_SERVER_CONFIG, 'Host', '')
		self._strMailServerPort = oConfig.get(MAIL_SERVER_CONFIG, 'Port', '')
		self._strDomain = oConfig.get(MAIL_SERVER_CONFIG, 'Domain', '')
		
	@property
	def	MailServerHost(self):
		return self._strMailServerHost
		
	@property
	def MailServerPort(self):
		return self._strMailServerPort
	
	@property	
	def DomainName(self):
		return self._strDomain
		
	# -------------------------------------------------------------------
	def LoadG8MailSenderConfig(self, oConfig):
		self._strG8MailSenderUsername = oConfig.get(G8_MAIL_SENDER_CONFIG, 'Username', '')
		self._strG8MailSenderPassword = oConfig.get(G8_MAIL_SENDER_CONFIG, 'Password', '')
		self._strG8MailAttachmentPath = oConfig.get(G8_MAIL_SENDER_CONFIG, 'AttachmentPath', '')
	
	@property
	def	G8MailSenderUsername(self):
		return self._strG8MailSenderUsername
	
	@property	
	def G8MailSenderPassword(self):
		return self._strG8MailSenderPassword
	
	@property	
	def G8MailAttachmentPath(self):
		return self._strG8MailAttachmentPath
		
	# -------------------------------------------------------------------
	def  LoadErrorLogConfig(self, oConfig):
		self._strErrorLog = oConfig.get('ERROR', 'ErrorLog', '')
	
	@property
	def	ErrorLogPath(self):
		return self._strErrorLog
	# -------------------------------------------------------------------
	def  LoadITSMAPIConfig(self, oConfig):
		self._strITSMAPILog = oConfig.get(ITSM_API_CONFIG, 'LogPath', '')
		self._strITSMAPIURLCreateIncident = oConfig.get(ITSM_API_CONFIG, 'UrlCreateIncident', '')
		self._strITSMAPIURLUpdateIncident = oConfig.get(ITSM_API_CONFIG, 'UrlUpdateIncident', '')
		self._strITSMAPIURLResolveIncident = oConfig.get(ITSM_API_CONFIG, 'UrlResolveIncident', '')
		self._strITSMAPIURLReopenIncident = oConfig.get(ITSM_API_CONFIG, 'UrlReopenIncident', '')
		
	# -------------------------------------------------------------------
	@property
	def ITSM_API_URL_CreateIncident(self):
		return self._strITSMAPIURLCreateIncident
		
	@property
	def ITSM_API_URL_UpdateIncident(self):
		return self._strITSMAPIURLUpdateIncident

	@property
	def ITSM_API_URL_ResolveIncident(self):
		return self._strITSMAPIURLResolveIncident
		
	@property
	def ITSM_API_URL_ReopenIncident(self):
		return self._strITSMAPIURLReopenIncident
	
	# -------------------------------------------------------------------
	@property
	def	ITSM_API_LogPath(self):
		return self._strITSMAPILog
		
	# -------------------------------------------------------------------
	def  LoadG8APIConfig(self, oConfig):
		self._strG8APILog = oConfig.get(G8_API_CONFIG, 'LogPath', '')
		self._strG8URLUpdateIncident = oConfig.get(G8_API_CONFIG, 'UrlUpdateIncident', '')
	# -------------------------------------------------------------------
	@property
	def G8_API_URL_UpdateIncident(self):
		return self._strG8URLUpdateIncident
	
	@property
	def	G8_API_LogPath(self):
		return self._strG8APILog
	# -------------------------------------------------------------------
	def  LoadCSAPIConfig(self, oConfig):
		self._strCSAPILog = oConfig.get(CS_API_CONFIG, 'LogPath', '')
		self._strCSURLUpdateIncident = oConfig.get(CS_API_CONFIG, 'UrlUpdateIncident', '')
		self._strCSURLListIncident = oConfig.get(CS_API_CONFIG, 'UrlLisIncident', '')
	# -------------------------------------------------------------------
	@property
	def CS_API_URL_UpdateIncident(self):
		return self._strCSURLUpdateIncident

	@property
	def CS_API_URL_ListIncident(self):
		return self._strCSURLListIncident
			
	@property
	def	CS_API_LogPath(self):
		return self._strCSAPILog
	# -------------------------------------------------------------------
	def  LoadPromotionAPIConfig(self, oConfig):
		self._strPromotionAPILog = oConfig.get(PROMOTION_API_CONFIG, 'LogPath', '')
		self._strPromotionURLCollectAlert = oConfig.get(PROMOTION_API_CONFIG, 'UrlCollectAlert', '')
	# -------------------------------------------------------------------
	@property
	def Promotion_API_URL_CollectAlert(self):
		return self._strPromotionURLCollectAlert

	@property
	def	Promotion_API_LogPath(self):
		return self._strPromotionAPILog
	# -------------------------------------------------------------------
	def  LoadScheduleConfig(self, oConfig):
		self._iSchedule	= int(oConfig.get('REALTIME', 'Schedule', ''))
	# -------------------------------------------------------------------
	def	 LoadMySqlMonitoringAssistantConfig(self, oConfig):
		self._strMySqlMonitorAsstHost		  = oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'Host', 'localhost')
		self._strMySqlMonitorAsstUser		  = oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'User', 'root')
		self._strMySqlMonitorAsstPassword	= oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'Password', 'sa')
		self._strMySqlMonitorAsstPort		  = oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'Port', '3306')
		self._strMySqlMonitorAsstSource		= oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'Source', '')
		self._strMySqlMonitorAsstPeriod		= oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'Period', 0)
		self._strMySqlMonitorAsstInsertLog = oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'InsertLog', '')
		self._strMySqlMonitorAsstUpdateLog = oConfig.get(MYSQLDB_MONITORING_ASSISTANT_CONFIG, 'UpdateLog', '')
	# -------------------------------------------------------------------
	# Define properties
	# -------------------------------------------------------------------
	@property
	def MySqlMonitorAssistantHost(self):
		return self._strMySqlMonitorAsstHost

	@property
	def MySqlMonitorAssistantUser(self):
		return self._strMySqlMonitorAsstUser

	@property
	def MySqlMMonitorAssistantPort(self):
		return self._strMySqlMonitorAsstPort

	@property
	def MySqlMMonitorAssistantPassword(self):
		return self._strMySqlMonitorAsstPassword

	@property
	def MySqlMonitorAssistantSource(self):
		return self._strMySqlMonitorAsstSource

	@property
	def MySqlMonitorAssistantPeriod(self):
		return self._strMySqlMonitorAsstPeriod
		
	@property
	def MySqlMonitorAssistantInsertLogPath(self):
		return self._strMySqlMonitorAsstInsertLog
		
	@property
	def MySqlMonitorAssistantUpdateLogPath(self):
		return self._strMySqlMonitorAsstUpdateLog
	# -------------------------------------------------------------------
	def	 LoadMongoMonitorAssistantConfig(self, oConfig):
		self._strMongoDBMonitorAsstHost     = oConfig.get(MONGODB_MONITORING_ASSISTANT_CONFIG, 'Host', 'localhost')
		self._strMongoDBMonitorAsstUser     = oConfig.get(MONGODB_MONITORING_ASSISTANT_CONFIG, 'User', 'admin')
		self._iMongoDBMonitorAsstPort       = int(oConfig.get(MONGODB_MONITORING_ASSISTANT_CONFIG, 'Port', '27017'))
		self._strMongoDBMonitorAsstPassword = oConfig.get(MONGODB_MONITORING_ASSISTANT_CONFIG, 'Password', '')
		self._strMongoDBMonitorAsstSource		= oConfig.get(MONGODB_MONITORING_ASSISTANT_CONFIG, 'Source', '')
		self._strMongoDBMonitorAsstUri      = "mongodb://" + self._strMongoDBMonitorAsstUser + ':' + self._strMongoDBMonitorAsstPassword
		self._strMongoDBMonitorAsstUri     +=	"@" + self._strMongoDBMonitorAsstHost + '/' + self._strMongoDBMonitorAsstSource
	# -------------------------------------------------------------------
	# Define properties
	# -------------------------------------------------------------------
	@property
	def MongoMonitorAssistantHost(self):
		return self._strMongoDBMonitorAsstHost

	@property
	def MongoMonitorAssistantUser(self):
		return self._strMongoDBMonitorAsstUser

	@property
	def MongoMonitorAssistantPort(self):
		return self._iMongoDBMonitorAsstPort

	@property
	def MongoMonitorAssistantPassword(self):
		return self._strMongoDBMonitorAsstPassword

	@property
	def MongoMonitorAssistantSource(self):
		return self._strMongoDBMonitorAsstSource
	
	@property
	def MongoMonitorAssistantUri(self):
		return self._strMongoDBMonitorAsstUri
	# -------------------------------------------------------------------
	def	 LoadMongoMasterConfig(self, oConfig):
		self._strMongoDBMasterHost     = oConfig.get(MONGODB_MASTER_CONFIG, 'Host', 'localhost')
		self._strMongoDBMasterUser     = oConfig.get(MONGODB_MASTER_CONFIG, 'User', 'admin')
		self._iMongoDBMasterPort       = int(oConfig.get(MONGODB_MASTER_CONFIG, 'Port', '27017'))
		self._strMongoDBMasterPassword = oConfig.get(MONGODB_MASTER_CONFIG, 'Password', '')
		self._strMongoDBMasterSource		= oConfig.get(MONGODB_MASTER_CONFIG, 'Source', '')
		self._strMongoDBMasterUri      = "mongodb://" + self._strMongoDBMasterUser + ':' + self._strMongoDBMasterPassword
		self._strMongoDBMasterUri     +=	"@" + self._strMongoDBMasterHost + '/' + self._strMongoDBMasterSource
	# -------------------------------------------------------------------
	# Define properties
	# -------------------------------------------------------------------
	@property
	def MongoMasterHost(self):
		return self._strMongoDBMasterHost

	@property
	def MongoMasterUser(self):
		return self._strMongoDBMasterUser

	@property
	def MongoMasterPort(self):
		return self._iMongoDBMasterPort

	@property
	def MongoMasterPassword(self):
		return self._strMongoDBMasterPassword

	@property
	def MongoMasterSource(self):
		return self._strMongoDBMasterSource
	
	@property
	def MongoMasterUri(self):
		return self._strMongoDBMasterUri
	# -------------------------------------------------------------------
	@property
	def HistoryLastPrefix(self):
		return self.strLastPrefix
	# -------------------------------------------------------------------
	def LoadLastPrefixParameterConfig(self, oConfig, strPart, strDateSuffix):
		try:
			self.strLastPrefix	= oConfig.get(strPart, 'Prefix', '')
		except:
			self.strLastPrefix  = strDateSuffix
			self.SetCurrentPrefix(strPart, strDateSuffix)
	
	# -------------------------------------------------------------------
	def	 GetPathFile(self):
		return self.strPath
	# -------------------------------------------------------------------
	def	 GetCurrentFileByDate(self, strDateSuffix, strProcessId):
		if strProcessId == FILE_LOG_NORMAL:
			self.strCurrentFile = "%s%s" % (self.strPrefix, strDateSuffix)
		else:
			self.strCurrentFile = "%s%s_%s" % (self.strPrefix, strDateSuffix, strProcessId)
		return self.strCurrentFile
	# -------------------------------------------------------------------
	def  GetCurrentFileByLog(self):
		return self.strCurrentFileByLog
	# -------------------------------------------------------------------
	def	 SetCurrentFile(self, strPart, strCurrentFile, strProcessId):
		try:
			self.strCurrentFileByLog = strCurrentFile
			oConfig = ConfigParser.ConfigParser()

			strfName = '%sParameter.ini' % self.GetName()
			fnConfig = os.path.join(os.path.dirname(__file__), strfName)
			oConfig.read(fnConfig)

			if strProcessId == FILE_LOG_NORMAL:
				oConfig.set(strPart, 'CurrentFile', strCurrentFile)
				f = open(fnConfig, 'w+')
				oConfig.write(f)

			else:
				strCurrentFileElement = 'CurrentFile_%s' % strProcessId
				oConfig.set(strPart, strCurrentFileElement, strCurrentFile)
				f = open(fnConfig, 'w+')
				oConfig.write(f)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

		finally:
			f.close()
	# -------------------------------------------------------------------
	def	 SetCurrentPrefix(self, strPart, strDateSuffix):
		try:
			oConfig = ConfigParser.ConfigParser()

			strfName = '%sParameter.ini' % self.GetName()
			fnConfig = os.path.join(os.path.dirname(__file__), strfName)
			oConfig.read(fnConfig)

			oConfig.set(strPart, 'Prefix', strDateSuffix)
			f = open(fnConfig, 'w+')
			oConfig.write(f)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

		finally:
			f.close()
	# -------------------------------------------------------------------
	def	 GetFileSize(self):
		return self.iFileSize
	# -------------------------------------------------------------------
	def	 SetFileSize(self, strPart, iFileSize, strProcessId):
		try:
			self.iFileSize = iFileSize
			oConfig = ConfigParser.ConfigParser()
			strfName = '%sParameter.ini' % self.GetName()
			fnConfig = os.path.join(os.path.dirname(__file__), strfName)
			oConfig.read(fnConfig)

			if strProcessId == FILE_LOG_NORMAL:
				oConfig.set(strPart, 'FileSize', iFileSize)
				f = open(fnConfig, 'w+')
				oConfig.write(f)
			else:
				strFileSizeElement = 'FileSize_%s' % strProcessId

				oConfig.set(strPart, strFileSizeElement, iFileSize)
				f = open(fnConfig, 'w+')
				oConfig.write(f)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)
		finally:
			f.close()
	# -------------------------------------------------------------------
	def	 GetFilePrefix(self):
		return self.strPrefix
	# -------------------------------------------------------------------
	def	 GetFilePosition(self):
		return self.iPosition
	# -------------------------------------------------------------------
	def	 SetFilePosition(self, strPart, iPosition, strProcessId):
		try:
			self.iPosition = iPosition
			oConfig = ConfigParser.ConfigParser()
			strfName = '%sParameter.ini' % self.GetName()
			fnConfig = os.path.join(os.path.dirname(__file__), strfName)
			oConfig.read(fnConfig)

			if strProcessId == FILE_LOG_NORMAL:
				oConfig.set(strPart, 'Position', iPosition)
				f = open(fnConfig, 'w+')
				oConfig.write(f)

			else:
				strPositionElement = 'Position_%s' % strProcessId

				oConfig.set(strPart, strPositionElement, iPosition)
				f = open(fnConfig, 'w+')
				oConfig.write(f)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)
		finally:
			f.close()
	# -------------------------------------------------------------------
	def	GetFileLog(self, strPart, strDateSuffix, strProcessId):
		try:
			#Main Calculate File Log Function
			strFileLog = ''
			if self.iFileSize == 0:	#Case First Load Config
				strCurrentFile = self.GetCurrentFileByDate(strDateSuffix, strProcessId)

				strFileLog = "%s/%s" % (self.strPath, strCurrentFile)

				if os.path.exists(strFileLog):
					iSizeFn = os.path.getsize(strFileLog)
					self.SetFileSize(strPart, iSizeFn, strProcessId)
					self.SetCurrentFile(strPart, strCurrentFile, strProcessId)
			else:

				strFullPathFileLog = "%s/%s" % (self.strPath, self.strCurrentFileByLog)
				#print self.strCurrentFileByLog
				#print strDateSuffix
				#strDateSuffix = Utilities.GetNextDatetimeSuffix(self.strCurrentFileByLog, iPeriod)

				if not os.path.exists(strFullPathFileLog):
					strCurrentFile = self.GetCurrentFileByDate(strDateSuffix, strProcessId)
					strFileLog = "%s/%s" % (self.strPath, strCurrentFile)
					if os.path.exists(strFileLog):
						iSizeFn = os.path.getsize(strFileLog)
						self.SetFileSize(strPart, iSizeFn, strProcessId)
						self.SetCurrentFile(strPart, strCurrentFile, strProcessId)
						self.SetFilePosition(strPart, 0, strProcessId)
				else:
					iSizeFn		= os.path.getsize(strFullPathFileLog)
					# Compare size between file log and size was stored in LogParser.ini
					# File Log was changed
					if iSizeFn > self.iFileSize:
						strFileLog = strFullPathFileLog
						self.SetFileSize(strPart, iSizeFn, strProcessId)

					# File Log wasn't changed
					elif iSizeFn == self.iFileSize:
						strCurrentFile = self.GetCurrentFileByDate(strDateSuffix, strProcessId)
						strFileLog = "%s/%s" % (self.strPath, strCurrentFile)
						if os.path.exists(strFileLog):
							iSizeFn = os.path.getsize(strFileLog)
							self.SetFileSize(strPart, iSizeFn, strProcessId)

							# New File Log
							if strCurrentFile != self.strCurrentFileByLog:
								self.SetCurrentFile(strPart, strCurrentFile, strProcessId)
								self.SetFilePosition(strPart, 0, strProcessId)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.oConfig)
		return strFileLog
	# -------------------------------------------------------------------
	def  GetPathDatePattern(self, strDateSuffix):
		strPathDatePattern = '%s/%s%s' % (self.strPath, self.strPrefix, strDateSuffix)
		return strPathDatePattern
	# -------------------------------------------------------------------
	def	 GetErrorLog(self):
		return self._strErrorLog
	# -------------------------------------------------------------------
	def  GetSchedule(self):
		return self._iSchedule
	# -------------------------------------------------------------------
	def	 GetMongoDBHost(self):
		return self._strMongoDBHost
	# -------------------------------------------------------------------
	def	 GetMongoDBUser(self):
		return self._strMongoDBUser
	# -------------------------------------------------------------------
	def	 GetMongoDBPassword(self):
		return self._strMongoDBPassword
	# -------------------------------------------------------------------
	def	 GetMongoDBPort(self):
		return self._iMongoDBPort
	# -------------------------------------------------------------------
	def	 GetMongoDBSource(self):
		return self._strMongoDBSource
	# -------------------------------------------------------------------
	def	 GetMongoDBUri(self):
		return self._strMongoDBUri
	# -------------------------------------------------------------------
	def	 CanStopService(self):
		oConfig = ConfigParser.ConfigParser()
		fnConfig = os.path.join(os.path.dirname(__file__), CONFIG_FILENAME)
		oConfig.read(fnConfig)
		return oConfig.get('REALTIME', 'Stop', 0)
	# -------------------------------------------------------------------
	def GetCriticalSeverity(self):
		return self._iCriticalSeverity
	# -------------------------------------------------------------------
	def GetWarningSeverity(self):
		return self._iWarningSeverity
	# -------------------------------------------------------------------
	def GetNormalSeverity(self):
		return self._iNormalSeverity
	# -------------------------------------------------------------------
	def GetUnknownSeverity(self):
		return self._iUnknownSeverity
	# -------------------------------------------------------------------
	def GetNoSeverity(self):
		return self._iNoSeverity
	# -------------------------------------------------------------------
	def GetHostAvailabilityUnknown(self):
		return self._iHostAvailabilityUnknown
	# -------------------------------------------------------------------
	def GetHostAvailabilityUp(self):
		return self._iHostAvailabilityUp
	# -------------------------------------------------------------------
	def GetHostAvailabilityDown(self):
		return self._iHostAvailabilityDown
	# -------------------------------------------------------------------
	def GetHostStatusMonitored(self):
		return self._iHostStatusMonitored
	# -------------------------------------------------------------------
	def GetHostStatusNotMonitored(self):
		return self._iHostStatusNotMonitored
	# -------------------------------------------------------------------
	def GetHostStatusTemplate(self):
		return self._iHostStatusTemplate
	# -------------------------------------------------------------------
	def GetHostStatusProxyActive(self):
		return self._iHostStatusProxyActive
	# -------------------------------------------------------------------
	def GetHostStatusProxyPassive(self):
		return self._iHostStatusProxyPassive
	# -------------------------------------------------------------------
	def GetHostTrapper(self):
		return self.m_strHostTrapper
	# -------------------------------------------------------------------
	def GetLocationTrapper(self):
		return self.m_strLocationTrapper
	# -------------------------------------------------------------------
	def GetZabbixServer(self):
		return self.m_strZabbixServer
	# -------------------------------------------------------------------