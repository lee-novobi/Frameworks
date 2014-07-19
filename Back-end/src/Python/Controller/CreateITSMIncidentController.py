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

class CCreateITSMIncidentController(CBaseController):
	def __init__(self, **kwargs):
		super(CCreateITSMIncidentController, self).__init__(**kwargs)
		
	# ---------------------------------------------------------------------------------------------- #
	def OpenWaitingIncident(self):
		try:
			oIncidentModel = CIncidentModel(self.m_oConfig)
			oAPI           = CITSMAPI(config=self.m_oConfig)
			
			if oIncidentModel is not None:
				arrWaitingInc = oIncidentModel.ListWaitingOpenIncident()
		
				for oIncident in arrWaitingInc:
					#oRs = oAPI.OpenIncidentTest(oIncident)
					if oIncident['ccutime'] is not None and oIncident['ccutime'] != "":
						oIncident['ccutime'] = int(oIncident['ccutime'])
					else:
						oIncident['ccutime'] = 0
					if oIncident['connection'] is not None and oIncident['connection'] != "":
						oIncident['connection'] = int(oIncident['connection'])
					else:
						oIncident['connection'] = 0
					if oIncident['customerimpacted'] is not None and oIncident['customerimpacted'] != "":
						oIncident['customerimpacted'] = int(oIncident['customerimpacted'])
					else:
						oIncident['customerimpacted'] = 0
					if oIncident['kb_id'] is not None and oIncident['kb_id'] != "":
						oIncident['kb_id'] = int(oIncident['kb_id'])
					else:
						oIncident['kb_id'] = 0
					
					#print oIncident
					oRs = oAPI.OpenIncident(oIncident)
					oIncidentWaiting = dict()
					oIncidentWaiting['status'] = INCIDENT_WAITING_STATUS_FAIL
					if oRs is not None:
						if oRs['status']==ITSM_STATUS_OK:
							try:
								self.InsertIncidentFollow(oIncidentModel, oRs, oIncident)
								if oIncident['source_from'] is not None:
									self.UpdateRawAlert(oIncidentModel, oRs, oIncident)
									self.UpdateCentralAlert(oIncidentModel, oRs, oIncident)
									if oIncident['source_from'].lower() == INCIDENT_SRC_FROM_G8:
										oIncident['itsm_id'] = oRs['itsm_id']
										self.G8Process(oIncidentModel, oIncident)
							except Exception, exc:
								strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
								Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
								pass
							oIncidentWaiting['status'] = INCIDENT_WAITING_STATUS_OPENED
							oIncidentWaiting['itsm_incident_id'] = oRs['itsm_id']
						oIncidentWaiting['sdk_last_msg'] = oRs['msg']
					else:
						oIncidentWaiting['sdk_last_msg'] = 'Unknown'
						
					oIncidentWaiting['sdk_last_insert_to_itsm'] = Utilities.GetCurrentTimeMySQLFormat()
					oIncidentWaiting['sdk_insert_to_itsm_count'] = {'type': MYSQL_VALUE_TYPE_EXPRESSION, 'value': 'IFNULL(sdk_insert_to_itsm_count,0) + 1'}
					oIncidentModel.UpdateIncidentWaiting({'id':oIncident['sdk_id']}, oIncidentWaiting)
				oIncidentModel.CloseMySQLConnection()
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			
	# ---------------------------------------------------------------------------------------------- #
	def InsertIncidentFollow(self, oIncidentModel, oAPIResult, oIncidentInfo):
		try:
			oIncidentFollow = dict()
			oIncidentFollow['itsm_incident_id']  = oAPIResult['itsm_id']
			oIncidentFollow['follow_shift_id']   = {'type': MYSQL_VALUE_TYPE_EXPRESSION, 'value': MYSQL_FUNCTION_GET_CURRENT_SHIFT_ID}
			if oIncidentInfo['source_from'] is not None:
				oIncidentFollow['source_from']     = oIncidentInfo['source_from']
				oIncidentFollow['source_id']       = oIncidentInfo['source_id']
				# Generate Json for linked alerts
				oIncidentFollow['linked_alerts']   = "[{\"src_from\": \"%s\", \"src_id\": \"%s\"}]" % (oIncidentInfo['source_from'], oIncidentInfo['source_id'])
				
			oIncidentFollow['title']             = oIncidentInfo['title']
			oIncidentFollow['created_by']        = oIncidentInfo['createdBy']
			oIncidentFollow['status']            = ITSM_STATUS_OPEN
			oIncidentFollow['created_date']      = Utilities.GetCurrentTimeMySQLFormat()
			oIncidentFollow['outage_start']      = oIncidentInfo['mysql_outagestart']
			oIncidentFollow['impact_level']      = oIncidentInfo['impact']
			oIncidentFollow['urgency_level']     = oIncidentInfo['urgency']
			oIncidentFollow['description']       = oIncidentInfo['description']
			oIncidentFollow['department']        = oIncidentInfo['department']
			oIncidentFollow['product']           = oIncidentInfo['service']
			if oIncidentInfo['customerimpacted'] is not None:
				oIncidentFollow['customer_case']   = oIncidentInfo['customerimpacted']
			oIncidentFollow['assignment']        = oIncidentInfo['assignmentGroup']
			oIncidentFollow['assignee']          = oIncidentInfo['assignee']
			oIncidentFollow['caused_by_external']= 't' if oIncidentInfo['causedbyexternalservice'] is not None and oIncidentInfo['causedbyexternalservice'].lower()=='true' else 'f'
			oIncidentFollow['open_by_sdk_tool']  = 1
			if oIncidentInfo['area'] is not None:
				oIncidentFollow['area']            = oIncidentInfo['area']
			if oIncidentInfo['subArea'] is not None:
				oIncidentFollow['subarea']         = oIncidentInfo['subArea']
			if oIncidentInfo['sdknote'] is not None:
				oIncidentFollow['sdknote']         = oIncidentInfo['sdknote']
			if oIncidentInfo['causedbydept'] is not None:
				oIncidentFollow['caused_by_external_dept'] = oIncidentInfo['causedbydept']
			if oIncidentInfo['detector'] is not None:
				oIncidentFollow['detector']          = oIncidentInfo['detector']
			if oIncidentInfo['mysql_downstart'] is not None:
				oIncidentFollow['downtime_start']    = oIncidentInfo['mysql_downstart']
			if oIncidentInfo['relateidchange'] is not None:
				oIncidentFollow['related_id_change'] = oIncidentInfo['relateidchange']
			if oIncidentInfo['relateid'] is not None:
				oIncidentFollow['related_id']        = oIncidentInfo['relateid']
			if oIncidentInfo['location'] is not None:
				oIncidentFollow['location']          = oIncidentInfo['location']
			if oIncidentInfo['criticalasset'] is not None:
				oIncidentFollow['critical_asset']    = oIncidentInfo['criticalasset']
			# New fields	
			if oIncidentInfo['ccutime'] is not None:
				oIncidentFollow['ccutime']    = oIncidentInfo['ccutime']
			if oIncidentInfo['connection'] is not None:
				oIncidentFollow['user_impacted']    = oIncidentInfo['connection']
			if oIncidentInfo['causecategory'] is not None:
				oIncidentFollow['rootcause_category']    = oIncidentInfo['causecategory']
			if oIncidentInfo['affactedCI'] is not None:
				oIncidentFollow['affected_ci']    = oIncidentInfo['affactedCI']
			if oIncidentInfo['causecategory'] is not None:
				oIncidentFollow['rootcause_category']    = oIncidentInfo['causecategory']
			if oIncidentInfo['auto_update_impact_level'] is not None:
				oIncidentFollow['auto_update_impact_level']    = oIncidentInfo['auto_update_impact_level']
			if oIncidentInfo['kb_id'] is not None:
				oIncidentFollow['kb_id'] = oIncidentInfo['kb_id']
			if oIncidentInfo['bug_category'] is not None:
				oIncidentFollow['bug_category'] = oIncidentInfo['bug_category']
			if oIncidentInfo['unit'] is not None:
				oIncidentFollow['unit'] = oIncidentInfo['unit']
		
			oIncidentModel.InsertIncidentFollow(oIncidentFollow)
		except Exception, exc:
			strErrorMsg = u'%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	# ---------------------------------------------------------------------------------------------- #
	def UpdateRawAlert(self, oIncidentModel, oAPIResult, oIncidentInfo):
		try:
			strSourceFrom = oIncidentInfo['source_from'].lower()
			
			if strSourceFrom != INCIDENT_SRC_FROM_SO6:
				oRawAlert = dict()
				oRawAlert['status']               = RAW_ALERT_STATUS_ITSM_OPENNED
				if strSourceFrom != INCIDENT_SRC_FROM_CS and strSourceFrom != INCIDENT_SRC_FROM_G8:
					oRawAlert['itsm_status']      = ITSM_STATUS_OPEN

				oRawAlert['itsm_status_notified'] = NO
				oRawAlert['itsm_id']              = oAPIResult['itsm_id']
				oIncidentModel.UpdateExternalRawAlert({'_id':ObjectId(oIncidentInfo['source_id'])}, oRawAlert, oIncidentInfo['source_from'])
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	# ---------------------------------------------------------------------------------------------- #
	def UpdateCentralAlert(self, oIncidentModel, oAPIResult, oIncidentInfo):
		try:
			oCentralAlert = dict()
			oCentralAlert['itsm_incident_id'] = oAPIResult['itsm_id']
			#thaodt signed
			oCentralAlert['is_acked'] = 1
			if oIncidentModel is not None:
				dictResult = oIncidentModel.GetAlertMessageFromCentralAlertCollection({'source_id':oIncidentInfo['source_id']})
				if dictResult is not None and len(dictResult) > 0:
					oCentralAlert['alert_message'] = 'Incident %s - %s' % (oAPIResult['itsm_id'], dictResult[0]['alert_message'])
				oIncidentModel.UpdateCentralAlert({'source_id':oIncidentInfo['source_id'], 'is_show': 1}, oCentralAlert)
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			pass
	# ---------------------------------------------------------------------------------------------- #
	def G8Process(self, oIncidentModel, oIncidentInfo):
		# Insert mailing list
		oRawAlert = oIncidentModel.LoadExternalRawAlert({'_id':ObjectId(oIncidentInfo['source_id'])}, INCIDENT_SRC_FROM_G8)

		if oRawAlert is not None and len(oRawAlert)>0:
			oSendMail = dict()
			oSendMail['to'] = oRawAlert[0]['to_email_list']
			oSendMail['cc'] = oRawAlert[0]['cc_email_list']
			oSendMail['attachment']  = oRawAlert[0]['attachments']
			oSendMail['source_from'] = INCIDENT_SRC_FROM_G8
			oSendMail['source_id']   = oIncidentInfo['source_id']
			oSendMail['ticket_id']   = oRawAlert[0]['ticket_id']
			oSendMail['itsm_id']     = oIncidentInfo['itsm_id']
			oSendMail['title']       = oRawAlert[0]['title']
			oSendMail['status']      = SEND_MAIL_STATUS_WAITING
			
			oIncidentModel.InsertMailingList(oSendMail)
	def Test(self):
		#oIncidentModel = CIncidentModel(self.m_oConfig)
		#oIncidentModel.UpdateMySQLDB({'id': 1}, {'title': u'[SDK][Backup] Thanh toán qua ATM không nhân được zing xu'}, 'incident_create_history')
		#oIncidentModel.CloseMySQLConnection()
		return
		
if __name__ == '__main__':
	while(True):
		print "Start CreateITSMIncident"
		try:
			oConfig = CConfig()
			oController = CCreateITSMIncidentController(config=oConfig)
			oController.OpenWaitingIncident()
			#oController.Test()
			exit
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			pass
		#print "End CreateITSMIncident"
		time.sleep(SLEEP_NOTIFY_ITSM_STATUS)
		