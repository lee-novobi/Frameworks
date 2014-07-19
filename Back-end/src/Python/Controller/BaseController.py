import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))

from inspect import stack
import re
from datetime import datetime
from Constants import *
from Config import *
from Utilities import Utilities

class CBaseController(object):
	def __init__(self, **arrArgs):
		self.m_oConfig = None
		try:
			oConfig = arrArgs.get("config", None)
			if oConfig is None:
				self.m_oConfig = CConfig()
			else:
				self.m_oConfig = oConfig
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
	