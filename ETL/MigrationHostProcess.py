# Description: main processing file for migration data
# encoding: utf-8
import sys, os
from Config import CConfig
from MigrationProcess import CMigrationThread
from Utility import Utilities
import time
from inspect import stack

#Class MigrationThread proccess migration data
class CMigrationHostThread(CMigrationThread):
	def __init__(self):
		super(CMigrationHostThread, self).__init__()

	#Run threading
	def SyncHostInfo(self):
		try:
			timeStart = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
			self._oTransportData.LoadAllConnection()
			self._oTransportData.PushHostsInfo()
			self._oTransportData.CloseAllMySqlConnection()
			timeEnd = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

if __name__ == '__main__':
	nStartTime = time.time()
	oConfig    = CConfig()
	try:
		oMigrationHostThread = CMigrationHostThread()

		if len(sys.argv) == 2:
			strFunction = sys.argv[1]

			if strFunction == "SyncHostData":
			    #******************************
			    # Insert new host from oda
			    #******************************
			    oMigrationHostThread.SyncHostInfo();

	except Exception, exc:
		strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
		Utilities.WriteErrorLog(strErrorMsg, oConfig)

	nEndTime = time.time()
	nDuration = nEndTime - nStartTime
	print 'Duration:%s' % nDuration
	Utilities.TransferDataTrapper2Zabbix("migration_monitor_assistant", nDuration, oConfig)
