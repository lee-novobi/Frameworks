# Description: Include some function connection database
# and implement transaction with mysql server
import MySQLdb
import MySQLdb.cursors
from Config import CConfig
from Utility import Utilities

#Class MySqlDataBase
#Interaction and transaction with mysql server
class CMySqlConnection:
	def __init__(self):
		# Create config object
		self._oConfig = CConfig()

	def Connect(self, oMysqlDriver):
		try:
			strDBHost 		= oMysqlDriver.GetHost()
			strDBUser 		= oMysqlDriver.GetUser()
			strDBPassword 	= oMysqlDriver.GetPassword()
			strDBSource		= oMysqlDriver.GetSource()
			self._connDb	= MySQLdb.connect(strDBHost, strDBUser, strDBPassword, strDBSource, cursorclass=MySQLdb.cursors.DictCursor)
			self._connDb.set_character_set('utf8')
			self._oCursorDb = self._connDb.cursor()
			self._oCursorDb.execute('SET NAMES utf8;')
			self._oCursorDb.execute('SET CHARACTER SET utf8;')
			self._oCursorDb.execute('SET character_set_connection=utf8;')
			return True
		except MySQLdb.Error, exc:
			strErrorMsg = 'Connect to %s error : %s' % (oMysqlDriver.GetHost(), str(exc)) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
		return False

	def Close(self):
		self._oCursorDb.close()

	def QueryAllData(self, strSQL):
		try:
			self._oCursorDb.execute(strSQL)
			return self._oCursorDb.fetchall()
		except MySQLdb.Error, exc:
			strErrorMsg = 'Query All Data Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
		return None

	def QueryOneData(self, strSQL):
		try:
			self._oCursorDb.execute(strSQL)
			return self._oCursorDb.fetchone()
		except MySQLdb.Error, exc:
			strErrorMsg = 'Query One Data Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
		return None

	def	 ExecuteSQL(self, strSQL):
		try:
			self._oCursorDb.execute(strSQL)
			self._connDb.commit()
		except MySQLdb.Error, exc:
			strErrorMsg = 'Execute SQL Error: %s' % str(exc) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
