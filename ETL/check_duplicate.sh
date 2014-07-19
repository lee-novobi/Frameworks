#!/bin/sh

PsUtil=`ps -ef | grep -c "/usr/local/bin/python2.7 "$1`

echo $PsUtil

##### EXECUTE #####
if [ $PsUtil -le 1 ];then
	curl --insecure --data "hostname=SDK_Monitor_MasterData_DB01&key=process_name&value=0" https://10.30.15.177/zabbix/services/zabbix_trapper.php
	/usr/local/bin/python2.7 $1
else
	echo $PsUtil
	curl --insecure --data "hostname=SDK_Monitor_MasterData_DB01&key=process_name&value=1" https://10.30.15.177/zabbix/services/zabbix_trapper.php
	echo [`date`]-$1 >> /home/oda/oda_monitor/process.log
	exit 1
fi
exit 0
