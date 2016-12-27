#!/bin/bash

LOCK_FILE=/tmp/initialized.lock

if [[ -f $LOCK_FILE ]]; then
	echo "Aready initialized!"
	exit 0
fi

SSDB_HOST=${SSDB_HOST:-ssdb}
SSDB_PORT=${SSDB_PORT:-8888}
USERNAME=${USERNAME:-admin}
PASSWORD=${PASSWORD:-admin123}

CONF_FILE=/var/www/html/app/config/config.php
sed -i "s/SSDB_HOST/${SSDB_HOST}/g" $CONF_FILE
sed -i "s/SSDB_PORT/${SSDB_PORT}/g" $CONF_FILE
sed -i "s/USERNAME/${USERNAME}/g" $CONF_FILE
sed -i "s/PASSWORD/${PASSWORD}/g" $CONF_FILE

echo "=== phpssdbadmin started with these configurations: ==="
echo " - SSDB_HOST: ${SSDB_HOST}"
echo " - SSDB_PORT: ${SSDB_PORT}"
echo " - USERNAME:  ${USERNAME}"
echo " - PASSWORD:  ${PASSWORD}"

touch $LOCK_FILE
