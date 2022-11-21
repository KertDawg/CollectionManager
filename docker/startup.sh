#!/bin/bash


cd /var/www/html

#  Start MySQL.
export MYSQL_ROOT_PASSWORD=justM3
service mariadb start
mysql < Accounts.sql 2>&1 > dbsetup.log
sed 's/utf8mb4_0900_ai_ci/utf8mb4_unicode_ci/g' Collections.sql | mysql Collections 2>&1 > dbsetup.log

#  Run the app setup.
cd /var/www/html/api
php ./setup.php
cd /var/www/html

#  Start apache.
service apache2 start

#  Keep running...
tail -f /dev/null
