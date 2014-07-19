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
from datetime import datetime

from Constants import *
from Utilities import Utilities
from BaseController import CBaseController

class CG8API(CBaseController):
	def __init__(self, **kwargs):
		super(CG8API, self).__init__(**kwargs)
	# -------------------------------------------------------------------
	def NotifyStatusChanged(self, oIncidentInfo):
		oResult = None
		try:
			strJsonSendInfo = json.dumps([{
																			'code':    oIncidentInfo['ticket_id']
																			,'date':   datetime.now().strftime(PYTHON_DATE_FORMAT_G8)
																			,'status': oIncidentInfo['itsm_status']
																	}])
			oRequestData = dict()
			oRequestData['json_send_info'] = strJsonSendInfo
			oRequestData['function']       = API_G8_FUNCTION_UPDATE_INCIDENT_STATUS
			oRequestData['model']          = API_G8_MODEL
			
			strRequestDataJson = json.dumps(oRequestData)
			
			Utilities.WriteLog(strRequestDataJson, self.m_oConfig.G8_API_LogPath)
			
			oRequest = urllib2.Request(self.m_oConfig.G8_API_URL_UpdateIncident)
			oRequest.add_header('Content-Type', 'application/json')
			
			strResponseContent = ''
			try:
				oResponse = urllib2.urlopen(oRequest, strRequestDataJson)
				strResponseContent = oResponse.read()
				print "CALL G8 UPDATE"
				print strResponseContent
				if strResponseContent is not None and strResponseContent != '':
					oResult = json.loads(strResponseContent)
				else:
					strResponseContent = 'Unknown Error.'
			except Exception, exc:
				strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				strResponseContent = strErrorMsg
				pass
			Utilities.WriteLog(strResponseContent + LOG_SEPERATOR, self.m_oConfig.G8_API_LogPath)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		return oResult
	# -------------------------------------------------------------------

