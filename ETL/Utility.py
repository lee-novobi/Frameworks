# encoding: utf-8
# Description: implement some common utilities
import time
import os, re, sys
from inspect import stack
import subprocess

# Class Utilities
# Define some functions interaction with file
class Utilities:
	def __init__(self):
		pass
	@staticmethod
	def WriteErrorLog(strErrorMsg, oConfig):
		try:
			strFileLog	= oConfig.GetLogParameter()
			fnLog		= open(strFileLog, "a")
			timeAt		= time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
			strMsg		= '[%s]: [%s]\r\n' % (timeAt, strErrorMsg)
			fnLog.write(strMsg)
		except Exception, exc:
			strErrorMsg = 'Error: %s' % str(exc) # give a error message
			print strErrorMsg

	@staticmethod
	def WriteLogTracking(timeStart, timeEnd):
		try:
			strFullFileLog = os.path.join(os.path.dirname(__file__), 'log_tracking.log')
			fLog = open(strFullFileLog, 'a')

			strMsg = 'Migration data process started at:%s\n' % timeStart
			strMsg += 'Migration data process ended at:%s\n' % timeEnd
			strMsg += 'Migration was successful.\n'
			strMsg += '===========================================\n'
			fLog.write(strMsg)
		except:
			print 'Unable write log'

	@staticmethod
	def CheckExistence(oResultSet):
		try:
			dFirstItem = oResultSet[0]
			return dFirstItem
		except Exception, exc:
			return False

	@staticmethod
	def  IsExistsSpecialChars(strValue, strPattern, oConfig):
		try:
			arrElement = re.compile(strPattern, re.M|re.I).findall(strValue)
			if len(arrElement) > 0:
				return True
			return False
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)

	@staticmethod
	def TransferDataTrapper2Zabbix(strProcessName, value, oConfig):
		try:
			try:
				value = int(value)
				command = 'curl --insecure --data \"hostname=%s&key=%s_%s_Time&value=%s\" https://%s/zabbix/services/zabbix_trapper.php' %(oConfig.GetHostTrapper(), oConfig.GetLocationTrapper(), strProcessName, value, oConfig.GetZabbixServer())
			except:
				command = 'curl --insecure --data \"hostname=%s&key=%s_%s_Time&value=\"%s\"\" https://%s/zabbix/services/zabbix_trapper.php' %(oConfig.GetHostTrapper(), oConfig.GetLocationTrapper(), strProcessName, value, oConfig.GetZabbixServer())

			#print command
			subprocess.call(command, shell=True)
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)

