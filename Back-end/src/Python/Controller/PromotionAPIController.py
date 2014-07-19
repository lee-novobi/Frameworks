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

class CPromotionAPI(CBaseController):
	def __init__(self, **kwargs):
		super(CPromotionAPI, self).__init__(**kwargs)
	# -------------------------------------------------------------------
	def CollectAlert(self):
		arrResult = None
		try:
			strURL       = self.m_oConfig.Promotion_API_URL_CollectAlert
			oResponse    = urllib2.urlopen(strURL)
			strRawResult = oResponse.read()
			arrResult = self.ParseAPIResult(strRawResult)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		return arrResult
	# -------------------------------------------------------------------
	def ParseAPIResult(self, strRawResult):
		arrResult = []
		try:
			Utilities.WriteLog(strRawResult + LOG_SEPERATOR, self.m_oConfig.Promotion_API_LogPath)
			strRawResult = strRawResult.replace("\r\n", "\n");
			arrResult    = json.loads((strRawResult));
			#print arrResult

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
		return arrResult
	# -------------------------------------------------------------------

