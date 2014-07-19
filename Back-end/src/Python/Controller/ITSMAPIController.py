import sys, os
import traceback
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

import re
import time
import json
import urllib
import urllib2
import base64
from inspect import stack
import cgi

from Constants import *
from Utilities import Utilities
from BaseController import CBaseController

class CITSMAPI(CBaseController):
	def __init__(self, **kwargs):
		super(CITSMAPI, self).__init__(**kwargs)
	# -------------------------------------------------------------------
	def OpenIncident(self, arrIncidentInfo):
		arrResult = None
		try:
			try:
				if arrIncidentInfo is not None and len(arrIncidentInfo)>0:
					#print arrIncidentInfo
					strJson = json.dumps(arrIncidentInfo, encoding="utf-8")
					#Utilities.WriteLog(strJson, self.m_oConfig.ITSM_API_LogPath)
					
					strIncidentInfo = urllib.quote_plus(strJson)
					strURL = self.m_oConfig.ITSM_API_URL_CreateIncident + strIncidentInfo
					print strURL
					#Utilities.WriteLog(strURL, self.m_oConfig.ITSM_API_LogPath)
					oResponse  = urllib2.urlopen(strURL)
					strRawResult = oResponse.read()
			except Exception, exc:
				strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				strRawResult = str(exc)
				pass
			arrResult = self.ParseAPIResult(strRawResult)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		return arrResult
	
	# -------------------------------------------------------------------
	def UpdateIncident(self, arrIncidentInfo):
		arrResult = None
		strRawResult = ''
		try:
			try:
				if arrIncidentInfo is not None and len(arrIncidentInfo)>0:
					strJson = json.dumps(arrIncidentInfo, encoding="utf-8")
					Utilities.WriteLog(strJson, self.m_oConfig.ITSM_API_LogPath)
					
					strIncidentInfo = urllib.quote_plus(strJson)
					strURL = self.m_oConfig.ITSM_API_URL_UpdateIncident + strIncidentInfo
					
					#print self.m_oConfig.ITSM_API_LogPath
					#Utilities.WriteLog(strURL, self.m_oConfig.ITSM_API_LogPath)
					oResponse  = urllib2.urlopen(strURL)
					strRawResult = oResponse.read()
			except Exception, exc:
				strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				strRawResult = str(exc)
				pass
			arrResult = self.ParseAPIResult(strRawResult)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		#print arrResult
		return arrResult
		
	# -------------------------------------------------------------------
	def UpdateIncidentStatus(self, arrIncidentInfo):
		strJson = ""
		strURL = ""
		arrResult = None
		strRawResult = ''
		try:
			try:
				if arrIncidentInfo is not None and len(arrIncidentInfo)>0:
					if arrIncidentInfo['incident_status'] == INCIDENT_STATUS_RESOLVE:
						arrResolvedInfo = dict() 
						try:
							if arrIncidentInfo['solution'] is not None:
								arrResolvedInfo['solution'] = arrIncidentInfo['solution'] 
						except:
							arrResolvedInfo['solution'] = ""
						arrResolvedInfo['id'] = arrIncidentInfo['id']
						arrResolvedInfo['outageend'] = arrIncidentInfo['outageend']
						arrResolvedInfo['closureCode'] = arrIncidentInfo['closureCode']
						
						strJson = json.dumps(arrResolvedInfo, encoding="utf-8")
						#Utilities.WriteLog(strJson, self.m_oConfig.ITSM_API_LogPath)
						
						strIncidentInfo = urllib.quote_plus(strJson)
						strURL = self.m_oConfig.ITSM_API_URL_ResolveIncident + strIncidentInfo
							
					if arrIncidentInfo['incident_status'] == INCIDENT_STATUS_REOPEN:
						arrReopenInfo = dict()
						arrReopenInfo['id'] = arrIncidentInfo['id']
						
						strJson = json.dumps(arrReopenInfo, encoding="utf-8")
						
						strIncidentInfo = urllib.quote_plus(strJson)
						strURL = self.m_oConfig.ITSM_API_URL_ReopenIncident + strIncidentInfo
						
					oResponse  = urllib2.urlopen(strURL)
					strRawResult = oResponse.read()
					#print strRawResult
			except Exception, exc:
				strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				strRawResult = str(exc)
				pass
			arrResult = self.ParseAPIResult(strRawResult)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		#print arrResult
		return arrResult
		
	# -------------------------------------------------------------------
	def ParseAPIResult(self, strRawResult):
		arrResult = {'msg': 'Init', 'status': ITSM_STATUS_INITIALIZE, 'itsm_id':None}
		try:
			strRawResult = strRawResult.replace("\r\n", "\n");
			
			Utilities.WriteLog(strRawResult + LOG_SEPERATOR, self.m_oConfig.ITSM_API_LogPath)
			
			arrTmpResult = dict()
			arrRawResult = strRawResult.split("\n")
			bIsMsg = False
			for strLine in arrRawResult:
				#print strLine
				arrKeyValue = strLine.strip().split(":")
				if strLine == 'Messages:':
					bIsMsg = True
					continue
				if bIsMsg is True:
					arrTmpResult['Messages'] = strLine
					bIsMsg = False
					continue
				
				if len(arrKeyValue)>1:
					arrTmpResult[arrKeyValue[0].strip()] = arrKeyValue[1].strip()
					strTmpKey = arrKeyValue[0].strip()
		
			if ITSM_INC_CREATE_PATTERN_SUCCESS in strRawResult and ITSM_INC_ID_KEY_PATTERN in arrTmpResult:
				arrResult['msg']     = SDK_INC_SUCCESS_PATTERN
				arrResult['status']  = ITSM_STATUS_OK
				arrResult['itsm_id'] = arrTmpResult[ITSM_INC_ID_KEY_PATTERN]
			elif ITSM_INC_UPDATE_PATTERN_SUCCESS in strRawResult and ITSM_INC_ID_KEY_PATTERN in arrTmpResult:
				arrResult['msg']     = SDK_INC_SUCCESS_PATTERN
				arrResult['status']  = ITSM_STATUS_OK
				arrResult['itsm_id'] = arrTmpResult[ITSM_INC_ID_KEY_PATTERN]
			elif ITSM_INC_RESOLVE_PATTERN_SUCCESS in strRawResult and ITSM_INC_ID_KEY_PATTERN in arrTmpResult:
				arrResult['msg']     = SDK_INC_SUCCESS_PATTERN
				arrResult['status']  = ITSM_STATUS_OK
				arrResult['itsm_id'] = arrTmpResult[ITSM_INC_ID_KEY_PATTERN]
			elif ITSM_INC_REOPEN_PATTERN_SUCCESS in strRawResult and ITSM_INC_ID_KEY_PATTERN in arrTmpResult:
				arrResult['msg']     = SDK_INC_SUCCESS_PATTERN
				arrResult['status']  = ITSM_STATUS_OK
				arrResult['itsm_id'] = arrTmpResult[ITSM_INC_ID_KEY_PATTERN]
			else:
				try:
					arrResult['msg'] = arrTmpResult['Messages']
				except: 
					arrResult['msg'] = strRawResult
				arrResult['status']  = ITSM_STATUS_FAIL

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return arrResult
	# -------------------------------------------------------------------
	def OpenIncidentTest(self, arrIncidentInfo):
		arrResult = {'msg': 'Init', 'status': ITSM_STATUS_OK, 'itsm_id':'Test ID'}
		return arrResult
	# -------------------------------------------------------------------

