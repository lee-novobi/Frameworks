#!/usr/local/bin/python2.7
# coding=utf8
import sys, os
from os import fork, chdir, setsid, umask
import traceback
import datetime
from inspect import stack
from smtplib import SMTP
from email.header import *
import email
from email.MIMEMultipart import MIMEMultipart
from email.MIMEBase import MIMEBase
from email.MIMEText import MIMEText
from email.MIMEImage import MIMEImage
from email.Utils import COMMASPACE, formatdate
from email import Encoders
import hashlib

sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Template'))

from mongokit import ObjectId

from Constants import *
from Config import *
from BaseController import CBaseController
from MailModel import CMailModel
from G8AlertModel import CG8AlertModel
from MailTemplate import CMailTemplate

class CMailCenterController(CBaseController):
	def __init__(self, **kwargs):
		super(CMailCenterController, self).__init__(**kwargs)
		self.m_oMailTemplate = CMailTemplate(self.m_oConfig)
		oConn = None
	
	# ---------------------------------------------------------------------------------------------- #
	def SendMailMessage(self, oConn, strEmailSubject, arrEmailTo, arrEmailCc, arrEmailBcc, strMessageBody, dFileAttachment, dContentFile):
		try:
			strEmailFrom = ""
			strEmailFrom = self.m_oConfig.G8MailSenderUsername

			oMsg = MIMEMultipart()
			oMsg.set_charset("utf-8")
			oMsg.attach(MIMEText(strMessageBody, "html", "utf-8"))

			oMsg['Subject'] = strEmailSubject
			oMsg['To'] = ", ".join(arrEmailTo)
			oMsg['Cc'] = ", ".join(arrEmailCc)
			oMsg['From'] = strEmailFrom
			
			arrToAddr = arrEmailTo + arrEmailCc + arrEmailBcc
			
			if len(dFileAttachment) > 0:
				for strAttachName in dFileAttachment:
					part = MIMEBase('application', "octet-stream")
					try:
						part.set_payload( open(dFileAttachment[strAttachName],"rb").read() )
					except Exception, exc:
						strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
						Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
					Encoders.encode_base64(part)
					part.add_header('Content-Disposition', 'attachment; filename="%s"' % strAttachName)
					oMsg.attach(part)

			if len(dContentFile) > 0:
				for strContentId in dContentFile:
					try:
						fimage = open(dContentFile[strContentId], 'rb')
						msgImage = MIMEImage(fimage.read())
						msgImage.add_header('Content-ID', strContentId)
						oMsg.attach(msgImage)
						fimage.close()
					except Exception, exc:
						strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
						Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

			oConn = SMTP(self.m_oConfig.MailServerHost, self.m_oConfig.MailServerPort)
			oConn.set_debuglevel(False)
			oConn.ehlo()
			oConn.starttls()
			oConn.ehlo()
			oConn.login(self.m_oConfig.G8MailSenderUsername, self.m_oConfig.G8MailSenderPassword)
			oConn.sendmail(strEmailFrom, arrToAddr, oMsg.as_string())
			oConn.quit()
			oConn.close()
			return {"status": ITSM_STATUS_OK, "msg": SDK_INC_SUCCESS_PATTERN}

		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			return {"status": ITSM_STATUS_FAIL, "msg": str(exc)}
	
	# ---------------------------------------------------------------------------------------------- #	
	def ConnectToMailServer(self):
		try:
			oConn = SMTP(self.m_oConfig.MailServerHost, self.m_oConfig.MailServerPort)
			# oConn.set_debuglevel(False)
			# oConn.ehlo()
			# oConn.starttls()
			# oConn.ehlo()
			# oConn.login(self.m_oConfig.G8MailSenderUsername, self.m_oConfig.G8MailSenderPassword)
			return oConn
			
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			return None
	
	# ---------------------------------------------------------------------------------------------- #	
	def DisconnectFromMailServer(self, oConn):
		try:
			oConn.close()

		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			
	# ---------------------------------------------------------------------------------------------- #
	def DeliveryMails(self):
		try:
			oMailModel = CMailModel(self.m_oConfig)
			oG8AlertModel = CG8AlertModel(self.m_oConfig)
			arrMails = oMailModel.ListWaitingSendMail()
			if len(arrMails) > 0:
				oConn = self.ConnectToMailServer()
				#bConnected = True
				if oConn is not None:
					for oMail in arrMails:
						# initialize variables
						arrMailTo = []
						arrMailCc = []
						arrMailBcc = []
						strMailSubject = ""
						strMailBody = ""
						dFileAttachment = dict()
						dContentFile = dict()

						dData = dict()
						if oMail['source_from'] == INCIDENT_SRC_FROM_G8:
							# prepare data for build template
							dData['Title'] 		= oMail['title']
							dData['TicketId'] 	= oMail['ticket_id']
							dData['IncidentId'] = oMail['itsm_id']
							
							oAlert = oG8AlertModel.GetG8AlertByTicketId(oMail['ticket_id'])
							if oAlert is not None:
								dData['Product']		= oAlert['product']
								dData['NumofCase']		= oAlert['num_of_case']
								dData['OutageStart']	= datetime.fromtimestamp(int( oAlert['outage_start'] )).strftime('%Y-%m-%d %H:%M:%S')
								dData['Description']	= oAlert['description']
							else:
								continue
								
							# render email content
							strMailSubject = G8_MAIL_INFORM_SUBJECT
							strMailBody +=  self.m_oMailTemplate.RenderG8InformMailContent(dData)
							strMailBody += self.m_oMailTemplate.RenderSignature(VNG_LOGO_CONTENT_ID)
							
							# render logo in signature
							dContentFile[VNG_LOGO_CONTENT_ID] = '../Template/' + VNG_LOGO_IMAGE
							
							# fetch attachments
							arrRequiredAttachment 	= oMail['attachment'].split(";")
							arrUnsavedAttachment 	= oMailModel.ListUnsavedAttachment(oMail['source_from'], oMail['ticket_id'])
							if len(arrUnsavedAttachment) > 0:
								continue
							else:
								arrSavedAttachment 		= oMailModel.ListSavedAttachment(oMail['source_from'], oMail['ticket_id'])
								if len(arrSavedAttachment) > 0:
									for oAttachment in arrSavedAttachment:
										strFilename = oAttachment['filename']
										strFilenameAlias = oAttachment['filename_alias']	
										dFileAttachment[strFilename] = self.m_oConfig.G8MailAttachmentPath + strFilenameAlias
						
							# render recipients
							if oMail['to'] is not None:
								arrMailTo = oMail['to'].split(";")
							if oMail['cc'] is not None:
								arrMailCc = oMail['cc'].split(";")
								arrMailBcc = ['duylh@vng.com.vn']
								
							#continue

						dResult = self.SendMailMessage(oConn, strMailSubject, arrMailTo, arrMailCc, arrMailBcc, strMailBody, dFileAttachment, dContentFile)
						oMailModel.UpdateMailStatus(oMail, dResult)
					#self.DisconnectFromMailServer(oConn)

		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		# ---------------------------------------------------------------------------------------------- #
		
def main():
	while(True):
		print "Start sending mails"
		try:
			oConfig = CConfig()
			oController = CMailCenterController(config=oConfig)
			oController.DeliveryMails()
			# break
			
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
			pass
		print "End sending mails"
		time.sleep(SLEEP_SEND_MAIL)
	
# Dual fork hack to make process run as a daemon
if __name__=='__main__':
	# try:
		# pid = fork()
		# if pid > 0:
			# exit(0)
	# except OSError, e:
		# exit(1)

	# chdir("/")
	# setsid()
	# umask(0)

	# try:
		# pid = fork()
		# if pid > 0:
			# exit(0)
	# except OSError, e:
		# exit(1)
 
	main()
	