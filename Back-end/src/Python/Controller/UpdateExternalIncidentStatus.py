import time
import sys, os
import traceback
from inspect import stack
from mongokit import ObjectId

sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))

from Constants import *
from Config import *
from BaseController import CBaseController
from IncidentModel import CIncidentModel
from G8APIController import CG8API
#from CSAPIController import CCSAPI

class CNotifyIncidentStatusController(CBaseController):
	def __init__(self, **kwargs):
		super(CNotifyIncidentStatusController, self).__init__(**kwargs)
	# -------------------------------------------------------------------
	def NotifyToG8(self):
		#print "NotifyToG8"
		try:
			oIncidentModel = CIncidentModel(self.m_oConfig)
			oAPI = CG8API(config=self.m_oConfig)
			if oIncidentModel is not None:
				arrInc = oIncidentModel.ListIncident2NotifyStatus(INCIDENT_SRC_FROM_G8)
				for oIncident in arrInc:
					try:
						oAPIResult = oAPI.NotifyStatusChanged(oIncident)
						if oAPIResult is not None and API_G8_RESPONSE_ERROR_CODE_KEY in oAPIResult:
							self.SetNotifiedStatus(oIncidentModel, oIncident, INCIDENT_SRC_FROM_G8)
					except Exception, exc:
						strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
						Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
						pass
				oIncidentModel.CloseMySQLConnection()
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
	# -------------------------------------------------------------------
	def SetNotifiedStatus(self, oIncidentModel, oIncident, srcSourceFrom):
		try:
			oIncidentModel.UpdateExternalRawAlert({'_id':oIncident['_id']},{'itsm_status_notified': YES}, srcSourceFrom)
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
	# -------------------------------------------------------------------
	def NotifyToCS(self):
		print "NotifyToCS"
		#oAPI = CCSAPI(config=self.m_oConfig)
		#oAPI.ListIncident()
		return		
# ---------------------------------------------------------------------
if __name__ == '__main__':
	nSleepInterval = SLEEP_DEFAULT
	while(True):
		print "Start NotifyIncidentStatus"
		try:
			oConfig = CConfig()
			
			strFunction = sys.argv[1]
			strFunction = strFunction.lower()
	
			oController = CNotifyIncidentStatusController()
			if strFunction == 'g8':
				oController.NotifyToG8()
				nSleepInterval = SLEEP_NOTIFY_ITSM_STATUS
			elif strFunction == 'cs':
				oController.NotifyToCS()
				nSleepInterval = 5
		except Exception, exc:
			strErrorMsg = strFunction + ' Error: %s - Line: %s' % (str(exc), sys.exc_traceback.tb_lineno)
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			pass
			
		#print "End NotifyIncidentStatus"
		time.sleep(nSleepInterval)
