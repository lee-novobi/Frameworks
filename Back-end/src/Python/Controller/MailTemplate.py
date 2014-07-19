#!/usr/local/bin/python2.7
# coding=utf8
import os, re, sys
from jinja2 import Template
from jinja2 import Environment, FileSystemLoader
from Config import *
from Utilities import Utilities
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Template'))

class CMailTemplate:
	def __init__(self, oConfig):
		self.m_oConfig = oConfig
		try:
			strTemplateDir = '%s%s' % ( os.path.abspath(os.path.join(os.path.dirname(os.path.realpath(__file__)), os.pardir)), '/Template/' )
			self.m_env = Environment(loader=FileSystemLoader( strTemplateDir ))
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		
	def RenderSignature(self, strContentId):
		try:
			oTemplate = self.m_env.get_template('Signature.tpl')
			strSignature = oTemplate.render(ContentId=strContentId)
			return strSignature
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			return ""
		
	def RenderG8InformMailContent(self, dictData):
		try:
			oTemplate = self.m_env.get_template('G8InformMailContent.tpl')
			strMailContent = oTemplate.render(dictData)
			return strMailContent
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			return ""
		