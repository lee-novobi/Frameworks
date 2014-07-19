from datetime import datetime
from mongokit import Document
from Config import CConfig

oConfig = CConfig()
strMongoDbName = oConfig.GetMongoDBSource()

#Class CMaintenances mapping maintenances collection
class CMaintenances(Document):
	def  __init__(self, oZCollection):
		super(CMaintenances, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'maintenanceid': long,
		'timeperiodid': long,
		'zbx_maintenanceid': long,
		'zbx_server_id': int,
		'name': basestring,
		'maintenance_type': int,
		'active_since': int,
		'active_till': int,
		'timeperiod_type': int,
		'every': int,
		'month': int,
		'dayofweek': int,
		'day': int,
		'start_time': int,
		'period': long,
		'start_date': int
	}
	required_fields = ['maintenanceid', 'timeperiodid']
	default_values = {
		'name': ""
		}

#Class CMaintenancesHosts mapping maintenances_hosts collection
class CMaintenancesHosts(Document):
	def  __init__(self, oZCollection):
		super(CMaintenancesHosts, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'maintenanceid': long,
		'hostid': long,
		'zbx_hostid': long,
		'zbx_server_id': int,
	}
	required_fields = ['maintenanceid', 'hostid']

#Class CMaintenancesGroups mapping maintenances_hosts collection
class CMaintenancesGroups(Document):
	def  __init__(self, oZCollection):
		super(CMaintenancesGroups, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'maintenanceid': long,
		'groupid': long,
		'zbx_groupid': long,
		'zbx_server_id': int,
	}
	required_fields = ['maintenanceid', 'groupid']

#Class CHosts mapping hosts collection
class CHosts(Document):
	def  __init__(self, oZCollection):
		super(CHosts, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'hostid': long,
		'zbx_hostid': long,
		'zbx_server_id': int,
		'is_deleted': int
	}
	required_fields = ['hostid', 'zbx_hostid', 'zbx_server_id']
	default_values = {
		'is_deleted': 0
		}
