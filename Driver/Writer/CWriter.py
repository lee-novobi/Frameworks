import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Controller'))

from inspect import stack
import mmap , time, re
import json
from datetime import datetime, timedelta
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
from Utilities import Utilities
from Constants import *
from MongodbController import *
from DriverConfig import Config

class Writer(MongodbController):
    def __init__(self):
        super(Writer, self).__init__()

    #**************************************************
    # Function: GetFileAttachment
    # Purpose: Get file attachment of incident
    #**************************************************
    def SaveFileAttachment(self):
        try:
            oExternalAttachmentCollection  = PymongoCollection(self.oDatabaseMongoMAdb, "external_attachments", False)
            print oExternalAttachmentCollection
            oRsResult = oExternalAttachmentCollection.find({'is_file_saved': 0})

            if Utilities.CheckExistence(oRsResult) is not False:
                for oFile in oRsResult:
                    strSourceId        = oFile['source_id']
                    try:
                        strFileNameAlias   = oFile['filename_alias']
                    except:
                        continue

                    strFileContent     = oFile['file_content']

                    f = open(os.path.join(DIR_PATH, strFileNameAlias), "w+b")
                    f.write(strFileContent)

                    oExternalAttachmentCollection.update({'source_id': strSourceId, 'filename_alias':strFileNameAlias, 'is_file_saved':0},
                                                         {'$set':{'is_file_saved':1}})

                f.close()
        except Exception, exc:
            strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, self.oConfig)

if __name__=='__main__':
    oWriter = Writer()
    while(True):
        try:
            oWriter.SaveFileAttachment()
        except Exception, exc:
            strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, oWriter.oConfig)
            pass
        time.sleep(3)
