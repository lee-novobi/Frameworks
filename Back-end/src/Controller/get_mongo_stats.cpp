// get_mongo_stats.cpp
#include <iostream>
#include <cstdlib>
#include <string>
#include <math.h>
#include <map>
#include "mongo/client/dbclient.h"

using namespace mongo;
vector<BSONObj> getDatabases(vector<BSONElement> &listElement);
int print_collection_total(DBClientConnection& conn, vector<BSONElement> &listElement, const char *strItem);
int print_list_database_info(DBClientConnection& conn, const char *strItem);
int print_server_stat(DBClientConnection& conn, const BSONObj& status, const char *strItem);
int print_db_stat(const BSONObj& stats, const char *strItem);

vector<BSONObj> getDatabases(vector<BSONElement> &listElement){
	vector<BSONObj> v;
	for( vector<BSONElement>::iterator it = listElement.begin(); it != listElement.end(); it++)
	{
		BSONObj bo = (*it).Obj();
		v.push_back(bo);
	}
	
	return v;
}

int print_collection_total(DBClientConnection& conn, vector<BSONElement> &listElement, const char *strItem){
	vector<BSONObj> boDatabases = getDatabases(listElement);
	
	int iCollections = 0;
	long iIndexs = 0;
	long iObjects = 0;
	
	for( vector<BSONObj>::iterator it = boDatabases.begin(); it != boDatabases.end(); it++)
	{
		BSONObj bo = (*it);
		BSONObj dbStat;
		
		string strDb = bo["name"].String();
		
		try {
			bool success = conn.runCommand( strDb,
											BSON( "dbStats" << 1 ),
											dbStat );
		}
		catch(...) {
			throw;
		}
		
		if(strcmp(strItem, "total_collection_count") == 0){
			iCollections += dbStat["collections"].Number();
		}
		
		if(strcmp(strItem, "total_index_count") == 0){
			iIndexs += dbStat["indexes"].Number();
		}
		
		if(strcmp(strItem, "total_object_count") == 0){
			iObjects += dbStat["objects"].Number();
		}
	}
	
	if(iCollections != 0){
		cout<< iCollections << endl;
		return 0;
	}
	
	if(iIndexs != 0){
		cout<< iIndexs << endl;
		return 0;
	}
	
	if(iObjects != 0){
		cout<< iObjects << endl;
		return 0;
	}
	
	cout<< 0 << endl;
	return 0;
}

int print_db_stat(const BSONObj& stats, const char *strItem){
	//db_avgObjSize
	if(strcmp(strItem, "db_avgObjSize") == 0){
		cout<< stats["avgObjSize"].Number() << endl;
		return 0;
	}
	
	//db_collections
	if(strcmp(strItem, "db_collections") == 0){
		cout<< stats["collections"].Number()<< endl;
		return 0;
	}
	
	//db_dataSize
	if(strcmp(strItem, "db_dataSize") == 0){
		cout<< stats["dataSize"].Number() << endl;
		return 0;
	}
	
	//db_numExtents
	if(strcmp(strItem, "db_numExtents") == 0){
		cout<< stats["numExtents"].Number() << endl;
		return 0;
	}
	
	//db_fileSize
	if(strcmp(strItem, "db_fileSize") == 0){
		cout<< stats["fileSize"].Number() << endl;
		return 0;
	}
	
	//db_indexes
	if(strcmp(strItem, "db_indexes") == 0){
		cout<< stats["indexes"].Number() << endl;
		return 0;
	}
	
	//db_indexSize
	if(strcmp(strItem, "db_indexSize") == 0){
		cout<< stats["indexSize"].Number() << endl;
		return 0;
	}
	
	//db_objects
	if(strcmp(strItem, "db_objects") == 0){
		cout<< stats["objects"].Number() << endl;
		return 0;
	}
	
	//db_storageSize
	if(strcmp(strItem, "db_storageSize") == 0){
		cout<< stats["storageSize"].Number() << endl;
		return 0;
	}
	
	cout<< 0 << endl;
	return 0;
}

int print_server_stat(DBClientConnection& conn, const BSONObj& status, const char *strItem){	
	//asserts
	if(strcmp(strItem, "asserts_msg") == 0){
		cout<< status["asserts"]["msg"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "asserts_regular") == 0){
		cout<< status["asserts"]["regular"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "asserts_user") == 0){
		cout<< status["asserts"]["user"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "asserts_warning") == 0){
		cout<< status["asserts"]["warning"].Number() << endl;
		return 0;
	}
	
	//backgroundFlushing
	if(strcmp(strItem, "backgroundFlushing_average_ms") == 0){
		cout<< status["backgroundFlushing"]["average_ms"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "backgroundFlushing_last_ms") == 0){
		cout<< status["backgroundFlushing"]["last_ms"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "backgroundFlushing_flushes") == 0){
		cout<< status["backgroundFlushing"]["flushes"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "backgroundFlushing_total_ms") == 0){
		cout<< status["backgroundFlushing"]["total_ms"].Number() << endl;
		return 0;
	}
	
	
	//cursors
	if(strcmp(strItem, "cursors_clientCursors_size") == 0){
		cout<< status["cursors"]["clientCursors_size"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "cursors_timedOut") == 0){
		cout<< status["cursors"]["timedOut"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "cursors_totalOpen") == 0){
		cout<< status["cursors"]["totalOpen"].Number() << endl;
		return 0;
	}
	
	//connections
	if(strcmp(strItem, "connections_available") == 0){
		cout<< status["connections"]["available"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "connections_current") == 0){
		cout<< status["connections"]["current"].Number() << endl;
		return 0;
	}
	
	//globalLock
	if(strcmp(strItem, "globalLock_currentQueue_readers") == 0){
		cout<< status["globalLock"]["currentQueue"]["readers"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "globalLock_currentQueue_total") == 0){
		cout<< status["globalLock"]["currentQueue"]["total"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "globalLock_currentQueue_writers") == 0){
		cout<< status["globalLock"]["currentQueue"]["writers"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "globalLock_lockTime") == 0){
		cout<< status["globalLock"]["lockTime"].Number() << endl;
		return 0;
	}
	
	//indexCounters
	if(strcmp(strItem, "indexCounters_btree_missRatio") == 0){
		cout<< status["indexCounters"]["btree"]["missRatio"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "indexCounters_btree_accesses") == 0){
		cout<< status["indexCounters"]["btree"]["accesses"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "indexCounters_btree_hits") == 0){
		cout<< status["indexCounters"]["btree"]["hits"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "indexCounters_btree_misses") == 0){
		cout<< status["indexCounters"]["btree"]["misses"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "indexCounters_btree_resets") == 0){
		cout<< status["indexCounters"]["btree"]["resets"].Number() << endl;
		return 0;
	}
	
	//extra_info
	if(strcmp(strItem, "extra_info_heap_usage_bytes") == 0){
		cout<< status["extra_info"]["heap_usage_bytes"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "extra_info_page_faults") == 0){
		cout<< status["extra_info"]["page_faults"].Number() << endl;
		return 0;
	}
	
	//mem
	if(strcmp(strItem, "mem_bits") == 0){
		cout<< status["mem"]["bits"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "mem_resident") == 0){
		cout<< status["mem"]["resident"].Number() << endl;
		return 0;
	}
	if(strcmp(strItem, "mem_virtual") == 0){
		cout<< status["mem"]["virtual"].Number() << endl;
		return 0;
	}
	
	//uptime
	if(strcmp(strItem, "uptime") == 0){
		cout<< status["uptime"].Number() << endl;
		return 0;
	}
	
	//opcounters_command
	if(strcmp(strItem, "opcounters_command") == 0){
		cout<< status["opcounters"]["command"].Number() << endl;
		return 0;
	}
	
	//mongo_version
	if(strcmp(strItem, "mongodb_version") == 0){
		cout<< status["version"].String() << endl;
		return 0;
	}
	
	print_list_database_info(conn, strItem);
	return 0;
}

int print_list_database_info(DBClientConnection& conn, const char *strItem){
	// Get Databases Info from listDatabases Command
	BSONObj dbList;
	try {
		bool success = conn.runCommand( string("admin"),
												BSON( "listDatabases" << 1 ),
												dbList );
	}
	catch(...) {
		return 1;
	}
	
	vector<BSONElement> listElement = dbList["databases"].Array();
	//db_count
	if(strcmp(strItem, "db_count") == 0){
		cout<< (dbList["databases"].Array()).size() << endl;
		return 0;
	}
	
	//database_info
	if(strcmp(strItem, "database_info") == 0){
		vector<BSONObj> boDatabases = getDatabases(listElement);
		string strDbInfo = "";
		strDbInfo += "{";
		
		for( vector<BSONObj>::iterator it = boDatabases.begin(); it != boDatabases.end(); it++)
		{
			BSONObj bo = (*it);
			strDbInfo = strDbInfo + " " + bo["name"].String() + ";" ;
		}
		
		strDbInfo = strDbInfo.substr(0, strDbInfo.size() -1);
		strDbInfo += " }";
		cout<< strDbInfo << endl;
		return 0;
	}
	
	//totalSize
	if(strcmp(strItem, "totalSize") == 0){
		long iTotalSize = dbList["totalSize"].Number();
		double fTotalSizeMb = (double)(iTotalSize/(1024*1024));
		fTotalSizeMb = ceilf(fTotalSizeMb * 100) / 100;
		cout<< fTotalSizeMb << endl;
		return 0;
	}
	
	print_collection_total(conn, listElement, strItem);
	return 0;
}

int main(int argc, const char **argv){
	const char *strDb   = "admin";
	const char *strUser = "";
	const char *strPass = "";
	const char *strServer = "127.0.0.1:27017";
	
	if(argc < 3){
		// Usage: get_mongo_stats <username> <password>
		std::cout << "Usage: get_mongo_stats <username> <password>" << endl;
		return 1;
	}
	strUser	= argv[1];
	strPass = argv[2];
	
	DBClientConnection conn;
	std::string errmsg;
	if(!conn.connect(strServer, errmsg)){
		cout<< "couldn't connect:" << errmsg << endl;
		return 1;
	}
	
	errmsg.clear();
	bool ok = conn.auth(strDb, strUser, strPass, errmsg);
	if(!ok)
		cout<< "connection error: " << errmsg << endl;
	
	MONGO_verify(ok);	
	
	// Get status server
	// Usage: get_mongo_stats <username> <password> <item>
	if(argc == 4)
	{
		BSONObj serverStatus;
		try {
			bool success = conn.runCommand( string("admin"),
													BSON( "serverStatus" << 1 ),
													serverStatus );
		}
		catch(...) {
			return 1;
		}
		print_server_stat(conn, serverStatus, argv[3]);
	}
	
	// Get stats database
	// Usage: get_mongo_stats <username> <password> <database> <item>
	if(argc == 5)
	{
		BSONObj dbStat;
		string strDb = argv[3];
		
		try {
			bool success = conn.runCommand( strDb,
											BSON( "dbStats" << 1 ),
											dbStat );
		}
		catch(...) {
			return 1;
		}
		print_db_stat(dbStat, argv[4]);
	}
	
	return 0;
}