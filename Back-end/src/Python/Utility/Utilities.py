# -*- coding: utf-8 -*-
# encoding: utf-8
# Description: implement some common utilities
import time
import os, re, sys
from inspect import stack
from datetime import datetime, timedelta
from math import *
import subprocess
import calendar
import codecs

sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
from Constants import *

sys.stdout = codecs.getwriter('utf_8')(sys.stdout)
sys.stdin = codecs.getreader('utf_8')(sys.stdin)

DEFINE_DEBUG = True

# Class Utilities
# Define some functions interaction with file
class Utilities():
	def __init__(self):
		pass
	
	# Get Date Suffix
	@staticmethod
	def	 GetDateSuffixByDay():
		strCurrentDay	= time.strftime("%Y%m%d", time.localtime())
		return strCurrentDay
	
	@staticmethod
	def	 GetDateSuffixByMonth():
		strCurrentDay	= time.strftime("%Y%m", time.localtime())
		return strCurrentDay

	@staticmethod
	def WriteErrorLog(strErrorMsg, oConfig):
		if DEFINE_DEBUG is False:
			return
		try:
			Utilities.WriteLog(strErrorMsg, oConfig.ErrorLogPath)
#			if oConfig is not None:
#				strFileLog	= oConfig.GetErrorLog()
#				fnLog		    = open(strFileLog, "a")
#				timeAt		  = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
#				strMsg		  = '[%s]: [%s]\r\n' % (timeAt, strErrorMsg)
#				fnLog.write(strMsg)
#			else:
#				print strMsg
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			print strErrorMsg
			
#	@staticmethod
#	def WriteITSMAPILog(strMsg, oConfig):
#		try:
#			if oConfig is not None:
#				dateAt      = time.strftime("%Y%m%d", time.localtime())
#				strFileLog	= oConfig.GetITSMAPILog() + dateAt
#				fnLog		    = open(strFileLog, "a")
#				timeAt		  = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
#				strMsg		  = '[%s]: [%s]\r\n' % (timeAt, strMsg)
#				fnLog.write(strMsg)
#			else:
#				print strMsg
#		except Exception, exc:
#			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
#			print strErrorMsg
			
	@staticmethod
	def WriteLog(strMsg, strLogPath):
		try:
			if strLogPath is not None and strLogPath != '':
				dateAt      = time.strftime("%Y%m%d", time.localtime())
				strFileLog  = strLogPath + dateAt
				fnLog       = codecs.open(strFileLog, "a", "utf-8")
				timeAt      = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
				#print strMsg
				if not isinstance(strMsg, unicode):
					strMsg    = unicode(strMsg, errors="ignore")
				strMsg      = u'[%s]: [%s]\r\n' % (timeAt, strMsg)
				
				fnLog.write(strMsg)
			else:
				print strMsg
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			print strErrorMsg
			pass

	@staticmethod
	def	 IsMatchString(strString, strSubString, oConfig):
		try:
			strPattern = strSubString
			strPattern = re.sub('[[]','\[', strPattern)
			strPattern = re.sub('[]]','\]', strPattern)

			arrMatch = re.compile(strPattern, re.M|re.I).findall(strString)

			if len(arrMatch) > 0:
				return 1
			return 0
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return 0

	@staticmethod
	def	 IsExistsSpecialChars(strValue, strPattern, oConfig):
		try:
			arrElement = re.compile(strPattern, re.M|re.I).findall(strValue)
			if len(arrElement) > 0:
				return 1
			return 0
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
	
	#*********************************************************
	#+Duymn
	# Add function IsIPCorrectly & IsMacAddressCorrectly
	# Date: 2013-07-03
	#*********************************************************
	@staticmethod
	def IsIPCorrectly(strIP, oConfig):
		try:
			bResult = False
			strPatternIP = '^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$'
			arrMatch = re.compile(strPatternIP, re.M|re.I).findall(strIP)

			if len(arrMatch) > 0:
				bResult = True

		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
		finally:
			return bResult

	@staticmethod
	def IsMacAddressCorrectly(strMacAddress, oConfig):
		try:
			bResult = False
			strPatternMacAddress = '[0-9a-f]{2}([-:])[0-9a-f]{2}(\\1[0-9a-f]{2}){4}$'
			arrMatch = re.compile(strPatternMacAddress, re.M|re.I).findall(strMacAddress)

			if len(arrMatch) > 0:
				bResult = True

		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
		finally:
			return bResult
	
	@staticmethod
	def GetIPType(strIP, oConfig):
		try:
			if strIP is not None and strIP != '':
				iStartPrivateIPClassA		= Utilities.DottedIPToInt('10.0.0.0', oConfig)
				iEndPrivateIPClassA			= Utilities.DottedIPToInt('10.255.255.255', oConfig)

				iStartPrivateIPClassB		= Utilities.DottedIPToInt('172.16.0.0', oConfig)
				iEndPrivateIPClassB			= Utilities.DottedIPToInt('172.31.255.255', oConfig)

				iStartPrivateIPClassC		= Utilities.DottedIPToInt('192.168.0.0', oConfig)
				iEndPrivateIPClassC			= Utilities.DottedIPToInt('192.168.255.255', oConfig)

				iInputIP					= Utilities.DottedIPToInt(strIP, oConfig)
				if iInputIP <= iEndPrivateIPClassA and iInputIP >= iStartPrivateIPClassA:
					return IP_PRIVATE
				elif iInputIP <= iEndPrivateIPClassB and iInputIP >= iStartPrivateIPClassB:
					return IP_PRIVATE
				elif iInputIP <= iEndPrivateIPClassC and iInputIP >= iStartPrivateIPClassC:
					return IP_PRIVATE
				return IP_PUBLIC
			else:
				return None
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return None

	@staticmethod
	def DottedIPToInt(dotted_ip, oConfig):
		try:
			exp = 3
			intip = 0
			for quad in dotted_ip.split('.'):
				intip = intip + (int(quad) * (256 ** exp))
				exp = exp - 1
			return intip
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return 0

	@staticmethod
	def	 GetProductInfo(strHostName, oConfig):
		try:
			arrElement = re.split('[-._]', strHostName)
			arrResult = [x for x in arrElement if x != '']

			if len(arrResult) > 0:
				return arrResult
			return []
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return []
	
	@staticmethod
	def	 GetKeyFunction(strKey, oConfig):
		try:
			strKeyFunction = ''
			arrElement = re.split('\[', strKey)
			strKeyFunction = arrElement[0]
			return strKeyFunction
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return ''

	@staticmethod
	def	 AppendUniqueArray(arrGroupA, arrGroupB, oConfig):
		try:
			arrTemp = [x for x in arrGroupA if x not in arrGroupB]
			for x in arrTemp:
				 arrGroupB.append(x)
			arrGroupB.sort()

			return arrGroupB

		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)

	@staticmethod
	def	 GetTimeMilestones(iInterval):
		try:
			timeMilesStones	  = datetime.now() - timedelta(minutes=iInterval)
			iUnixTimeStamp	  = int(timeMilesStones.strftime("%s"))
			return iUnixTimeStamp
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg)

	@staticmethod
	def CheckExistence(oResultSet):
		try:
			dFirstItem = oResultSet[0]
			return dFirstItem
		except Exception, exc:
			return False

	@staticmethod
	def GetMinMaxClock():
		try:
			timeLocal		= datetime.now()
			dtCurrentDate	= datetime.now()

			iHours			= timeLocal.hour
			iMinutes		= timeLocal.minute
			iSeconds		= timeLocal.second
			iMicroseconds	= timeLocal.microsecond

			timeBegin = timeLocal - timedelta(hours=iHours, minutes=iMinutes, seconds=iSeconds, microseconds=iMicroseconds)

			iMaxClock		= int(timeLocal.strftime("%s"))
			#print "Date:%s	 -	%s" % (timeLocal,iMaxClock)

			iMinClock		= int(timeBegin.strftime("%s"))
			#print "Date:%s	 -	%s" % (timeBegin,iMinClock)

			dtCurrentDate	= timeBegin

		except Exception, exc:
			strErrorMsg = 'Get min max clock error: %s - Line: %s' % (str(exc),sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg)
		finally:
			return iMinClock, iMaxClock, dtCurrentDate

	@staticmethod
	def GetTimeDelta2Date(dtStartDate, dtEndDate):
		try:
			iTotal = 0
			dd = dtEndDate - dtStartDate
			iTotal = dd.days
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			iTotal = -1
		finally:
			return iTotal

	#*******************************************************
	# Function: GetMonthQuarterYearByClock
	# Description: Get month and quarter and year from clock
	# Parameter: iClock
	# Result: month & quarter & year
	#*******************************************************
	@staticmethod
	def GetMonthQuarterYearByClock(iClock, oConfig):
		try:
			oDatetime = datetime.fromtimestamp(int(iClock))

			tupleDatetime = oDatetime.timetuple()

			strMonth		= time.strftime("%m", tupleDatetime)
			strYear			= time.strftime("%Y", tupleDatetime)

			iMonth		= float(strMonth)
			iNumOfMonth = PARTITION_FORMAT["quarter"]
			iQuarter	= int(ceil(float(iMonth)/float(iNumOfMonth)))

			return int(strMonth), iQuarter, int(strYear)
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return 1, 1, 1900
	#*******************************************************

	@staticmethod
	def	 SplitStringByPattern(strValue, strPattern, oConfig):
		try:
			arrElement = re.split(strPattern, strValue)
			arrResult = [x for x in arrElement if x != '']

			if len(arrResult) > 0:
				return arrResult
			return []
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			return []

	@staticmethod
	def IsSame2Array(arrayA, arrayB, oConfig):
		try:
			bResult = True

			if len(arrayA) != len(arrayB):
				return False

			for keyA in arrayA:
				if keyA not in arrayB:
					return False

			return bResult
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)

	@staticmethod
	def GetSizeUnit(fSize, oConfig):
		try:
			iGB		   = 1024
			iSize	   = int(ceil(fSize/iGB))

			iTotalSize = int(ceil(iSize/10.0)) * 10
			return iTotalSize
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
			
	@staticmethod
	def GetCurrentTimeMySQLFormat(oConfig=None, **arrArgs):
		try:
			oDate = arrArgs.get("date", None)
			if oDate is None:
				oDate = datetime.now()
			return oDate.strftime(PYTHON_DATE_FORMAT_MYSQL)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			if oConfig is not None:
				Utilities.WriteErrorLog(strErrorMsg, oConfig)
			else:
				print strErrorMsg
		