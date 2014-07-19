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
from IncidentModel import CIncidentModel
from ITSMAPIController import CITSMAPI

class CUpdateITSMIncidentController(CBaseController):
	def __init__(self, **kwargs):
		super(CUpdateITSMIncidentController, self).__init__(**kwargs)
		
	# ---------------------------------------------------------------------------------------------- #
	def UpdateWaitingIncident(self):
		try:
			#print 'Updating ......'
			oIncidentModel = CIncidentModel(self.m_oConfig)
			oAPI           = CITSMAPI(config=self.m_oConfig)
			
			if oIncidentModel is not None:
				arrWaitingInc = oIncidentModel.ListWaitingUpdateIncident()
				#print arrWaitingInc

				for oIncident in arrWaitingInc:
					#print oIncident
					if oIncident['ccutime'] is not None and oIncident['ccutime'] != "":
						oIncident['ccutime'] = int(oIncident['ccutime'])

					if oIncident['connection'] is not None and oIncident['connection'] != "":
						oIncident['connection'] = int(oIncident['connection'])
						
					if oIncident['customerimpacted'] is not None and oIncident['customerimpacted'] != "":
						oIncident['customerimpacted'] = int(oIncident['customerimpacted'])
					
					# print oIncident	
					for k, v in oIncident.items():
						if v is None:
							del oIncident[k]
					#print oIncident
					
					oRs = oAPI.UpdateIncident(oIncident)
					
					oRsUpdateStatus = None
					try:
						if oIncident['incident_status'] == INCIDENT_STATUS_RESOLVE or oIncident['incident_status'] == INCIDENT_STATUS_REOPEN:
							oRsUpdateStatus = oAPI.UpdateIncidentStatus(oIncident)
						else: 
							oRsUpdateStatus = oRs
					except: 
						oRsUpdateStatus = oRs
						pass
						
					oUpdateData = dict()
					oUpdateData['sdk_update_to_itsm_status'] = ITSM_STATUS_FAIL
					if oRs is not None and oRsUpdateStatus is not None:
						if oRs['status'] == ITSM_STATUS_OK and oRsUpdateStatus['status'] == ITSM_STATUS_OK:
							oUpdateData['sdk_update_to_itsm_status'] = ITSM_STATUS_OK
								
						if oRs['msg'] == oRsUpdateStatus['msg']:
							oUpdateData['sdk_last_msg'] = oRs['msg']
						else:
							oUpdateData['sdk_last_msg'] = '{"normal_update_msg": "%s", "status_update_msg": "%s"' % (oRs['msg'], oRsUpdateStatus['msg'])
					else:
						oUpdateData['sdk_last_msg'] = 'Unknown'
					oUpdateData['sdk_last_update_to_itsm'] = Utilities.GetCurrentTimeMySQLFormat()
					oUpdateData['sdk_update_to_itsm_count'] = {'type': MYSQL_VALUE_TYPE_EXPRESSION, 'value': 'IFNULL(sdk_update_to_itsm_count,0) + 1'}

					oIncidentModel.UpdateWaitingUpdateIncident({'id': oIncident['sdk_id']}, oUpdateData)
					
			oIncidentModel.DeleteIncidentUpdateSuccess()
					
			oIncidentModel.CloseMySQLConnection()
			
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			
			
if __name__ == '__main__':
	while(True):
		#print "Start UpdateITSMIncident"
		try:
			oConfig = CConfig()
			oController = CUpdateITSMIncidentController(config=oConfig)
			oController.UpdateWaitingIncident()
			#oController.Test()
			exit
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			pass
		#print "End CreateITSMIncident"
		time.sleep(SLEEP_NOTIFY_ITSM_STATUS)
		