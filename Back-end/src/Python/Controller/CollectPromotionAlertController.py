#!/usr/local/bin/python2.7
# coding=utf8
import sys, os
import traceback
from inspect import stack

sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))

from mongokit import ObjectId

from Constants import *
from Config import *
from BaseController import CBaseController
from PromotionAlertModel import CPromotionAlertModel
from PromotionAPIController import CPromotionAPI

class CCollectPromotionAlertController(CBaseController):
	def __init__(self, **kwargs):
		super(CCollectPromotionAlertController, self).__init__(**kwargs)
		
	# ---------------------------------------------------------------------------------------------- #
	def CollectAlert(self):
		try:
			oPromotionModel = CPromotionAlertModel(self.m_oConfig)
			oAPI            = CPromotionAPI(config=self.m_oConfig)
			
			arrProcessingAlert = []
			arrPromotionAlert = oAPI.CollectAlert()
			for oNewRawAlert in arrPromotionAlert:
				try:
					# print "abc ---------------------------------"
					strAlertKey = str(oNewRawAlert['ProgramID']) + "_" + oNewRawAlert['API'].encode('ascii', 'replace')
					# print strAlertKey
					arrProcessingAlert.append(strAlertKey)
					
					oNewRawAlert['ProgramID'] = strAlertKey
					oOldRawAlert = oPromotionModel.LoadRawAlert(strAlertKey)
					oPromotionModel.WriteRawAlert(oNewRawAlert, oOldRawAlert)
					oPromotionModel.WriteCentralAlert(oNewRawAlert, oOldRawAlert)
				except Exception, exc:
					strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
					pass
			oPromotionModel.TurnOffRawAlert(arrProcessingAlert)
			oPromotionModel.TurnOffCentralAlert(arrProcessingAlert)
			oPromotionModel.CloseMySQLConnection()
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
	# ---------------------------------------------------------------------------------------------- #
		
if __name__ == '__main__':
	while(True):
		print "Start CollectPromotionAlert"
		try:
			oConfig = CConfig()
			oController = CCollectPromotionAlertController(config=oConfig)
			oController.CollectAlert()
			exit
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			pass
		#print "End CreateITSMIncident"
		time.sleep(SLEEP_PROMOTION_ALERT)
		