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
from SOAPpy import WSDL

from Constants import *
from Utilities import Utilities
from BaseController import CBaseController

class CCSAPI(CBaseController):
	def __init__(self, **kwargs):
		super(CCSAPI, self).__init__(**kwargs)
	# -------------------------------------------------------------------
	def ListIncident(self):
		oResult = None
		try:
			oServer = WSDL.Proxy(self.m_oConfig.CS_API_URL_ListIncident)
			oServer.config.dumpSOAPOut = 1
			oServer.config.dumpSOAPIn  = 1
			
			oResult = oServer.GetListINC("sdk123")
			print oResult
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		return oResult
	# -------------------------------------------------------------------


