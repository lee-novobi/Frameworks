# encoding: utf-8
# Description: implement some common utilities
import time
import os, re, sys
from inspect import stack
from datetime import datetime, timedelta
from math import *
import subprocess
import calendar

sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
from Constants import *

DEFINE_DEBUG = True

# Class Utilities
# Define some functions interaction with file
class Utilities():
	def __init__(self):
		pass

	@staticmethod
	def CheckExistence(oResultSet):
		try:
			dFirstItem = oResultSet[0]
			return dFirstItem
		except Exception, exc:
			return False

	@staticmethod
	def WriteErrorLog(strErrorMsg, oConfig):
		if DEFINE_DEBUG is False:
			return
		try:
			strFileLog	= oConfig.GetErrorLog()
			fnLog		= open(strFileLog, "a")
			timeAt		= time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
			strMsg		= '[%s]: [%s]\r\n' % (timeAt, strErrorMsg)
			fnLog.write(strMsg)
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			print strErrorMsg