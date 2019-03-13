#!/bin/bash
MASTER_HOST=$1
PORT=$2
USER_DUMP=$3
PASS_DUMP=$4
DB=$5
USER_SLAVE=$6
PASS_SLAVE=$7
USER_LOCAL=$8
PASS_LOCAL=$9
HOST_LOCAL=${10}
PORT_LOCAL=${11}
CHANNEL=${12}

if [ ! -d storage/$DB ]; then
    mkdir -p storage/$DB
fi

DUMP_FILE="storage/$DB/$DB-export-$(date +"%Y%m%d-%H%M%S").sql"

##
# MASTER
# ------
# Export database and read log position from master, while locked
##

echo "----- RUN ON MASTER -----"
echo "  - Lock database $MASTER_HOST"

mysql -h$MASTER_HOST -u$USER_DUMP -p$PASS_DUMP -P$PORT $DB <<-EOSQL &
	FLUSH PRIVILEGES;
	FLUSH TABLES WITH READ LOCK;
	DO SLEEP(3600);
EOSQL

echo "  - Waiting for database to be locked"
sleep 3

# Dump the database (to the client executing this script) while it is locked
echo "  - Dumping database to $DUMP_FILE"
mysqldump -h$MASTER_HOST -u$USER_DUMP -p$PASS_DUMP -P$PORT $DB > $DUMP_FILE
echo "  - Dump complete"

# Take note of the master log position at the time of dump
MASTER_STATUS=$(mysql -h$MASTER_HOST -u$USER_DUMP -p$PASS_DUMP -P$PORT -ANe "SHOW MASTER STATUS;" | awk '{print $1 " " $2}')
LOG_FILE=$(echo $MASTER_STATUS | cut -f1 -d ' ')
LOG_POS=$(echo $MASTER_STATUS | cut -f2 -d ' ')
echo "  - Current log file is $LOG_FILE and log position is $LOG_POS"

# When finished, kill the background locking command to unlock
kill $! 2>/dev/null
wait $! 2>/dev/null

echo "  - Master database unlocked"

sleep 2
# create cache credit
mysql -h$MASTER_HOST -u$USER_SLAVE -p$PASS_SLAVE -P$PORT -e "exit"

##
# SLAVES
# ------
# Import the dump into slaves and activate replication with
# binary log file and log position obtained from master
##

echo "----- RUN ON SLAVE -----"
echo "  - Stop slave for channel '$CHANNEL'"
sleep 2
mysql -h$HOST_LOCAL -u$USER_LOCAL -p$PASS_LOCAL -P$PORT_LOCAL -e "STOP SLAVE FOR CHANNEL '$CHANNEL';"

echo "  - Creating database copy"
mysql -h$HOST_LOCAL -u$USER_LOCAL -p$PASS_LOCAL -P$PORT_LOCAL -e "DROP DATABASE IF EXISTS $DB; CREATE DATABASE $DB;"
# scp $DUMP_FILE $HOST_LOCAL:$DUMP_FILE >/dev/null
sleep 2
mysql -h$HOST_LOCAL -u$USER_LOCAL -p$PASS_LOCAL -P$PORT_LOCAL $DB < $DUMP_FILE

echo "  - Setting up slave replication"
sleep 2
mysql -h$HOST_LOCAL -u$USER_LOCAL -p$PASS_LOCAL -P$PORT_LOCAL <<-EOSQL &
    CHANGE MASTER TO MASTER_HOST='$MASTER_HOST',
    MASTER_USER='$USER_SLAVE',
    MASTER_PASSWORD='$PASS_SLAVE',
    MASTER_LOG_FILE='$LOG_FILE',
    MASTER_LOG_POS=$LOG_POS
    FOR CHANNEL '$CHANNEL';
EOSQL

# CHANGE REPLICATION FILTER REPLICATE_REWRITE_DB = (($DB, $DBSlave)) FOR CHANNEL '$CHANNEL';

echo "START SLAVE CHANNEL: $CHANNEL"
sleep 2
mysql -h$HOST_LOCAL -u$USER_LOCAL -p$PASS_LOCAL -P$PORT_LOCAL -e "START SLAVE FOR CHANNEL '$CHANNEL';"
# pass authenticated user in processlist mysql
sleep 2
mysql -h$MASTER_HOST -u$USER_SLAVE -p$PASS_SLAVE -P$PORT -e "exit"

# Wait for slave to get started and have the correct status
# Check if replication status is OK
echo "Check status";
sleep 5
SLAVE_STATUS=$(mysql -h$HOST_LOCAL -u$USER_LOCAL -p$PASS_LOCAL -P$PORT_LOCAL -e "SHOW SLAVE STATUS FOR CHANNEL '$CHANNEL' \G")
SLAVE_OK=$(echo "$SLAVE_STATUS" | grep 'Waiting for master')
if [ -z "$SLAVE_OK" ]; then
    echo "  - Error ! Wrong slave IO state."
else
    echo "  - Slave IO state OK"
fi
