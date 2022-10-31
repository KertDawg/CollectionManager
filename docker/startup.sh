#!/bin/bash


cd /var/www/html

#  Start MySQL.
export MYSQL_ROOT_PASSWORD=justM3
service mariadb start
mysql < kertos.sql 2>&1 > dbsetup.log

#  Start apache.
service apache2 start

#  Keep running...
tail -f /dev/null
