#!/bin/bash
MASTER_HOST=$1
PORT=$2
USER=$3
PASS=$4
DB=$5

if [ ! -d storage/$DB ]; then
    mkdir -p storage/$DB
fi

DUMP_FILE="storage/zabbix/$DB-export-$(date +"%Y%m%d-%H%M%S").sql"

##
# MASTER
# ------
# Export database and read log position from master, while locked
##

echo "----- RUN ON MASTER -----"
echo "  - Lock database $MASTER_HOST"

mysql -h$MASTER_HOST -u$USER -p$PASS -P$PORT $DB <<-EOSQL &
	FLUSH PRIVILEGES;
	FLUSH TABLES WITH READ LOCK;
	DO SLEEP(3600);
EOSQL

echo "  - Waiting for database to be locked"
sleep 3

# Dump the database (to the client executing this script) while it is locked
echo "  - Dumping database to $DUMP_FILE"
mysqldump -h$MASTER_HOST -u$USER -p$PASS -P$PORT $DB > $DUMP_FILE
echo "  - Dump complete"

# Take note of the master log position at the time of dump
MASTER_STATUS=$(mysql -h$MASTER_HOST -u$USER -p$PASS -P$PORT -ANe "SHOW MASTER STATUS;" | awk '{print $1 " " $2}')
LOG_FILE=$(echo $MASTER_STATUS | cut -f1 -d ' ')
LOG_POS=$(echo $MASTER_STATUS | cut -f2 -d ' ')
echo "  - Current log file is $LOG_FILE and log position is $LOG_POS"

# When finished, kill the background locking command to unlock
kill $! 2>/dev/null
wait $! 2>/dev/null

echo "  - Master database unlocked"

echo "channel: $DB"
echo "filename: $DUMP_FILE"
echo "log_file: $LOG_FILE"
echo "log_pos: $LOG_POS"
