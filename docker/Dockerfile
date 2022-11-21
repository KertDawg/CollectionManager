FROM php:8.1-apache


#  Clear old web files.
WORKDIR /var/www/html
RUN rm -rf *

#  Update and install software.
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y unzip net-tools procps mariadb-server
RUN a2enmod rewrite
RUN docker-php-ext-install pdo_mysql

#  Create folders.
RUN mkdir -p /var/www/html/api
RUN mkdir -p /var/www/html/api/apps
RUN mkdir -p /var/www/html/api/controllers
RUN mkdir -p /var/www/html/api/keys
RUN mkdir -p /var/www/html/api/utils
RUN mkdir -p /var/www/html/api/vendor

#  Make real error logs.
RUN rm -f /var/log/apache2/error.log
RUN touch /var/log/apache2/error.log
RUN rm -f /var/log/apache2/access.log
RUN touch /var/log/apache2/access.log

#  Install web client.
COPY spa/ /var/www/html/

#  Install web server.
COPY startup.sh /var/www/html/
COPY Accounts.sql /var/www/html/
COPY Collections.sql /var/www/html/
COPY server/controllers/ /var/www/html/api/controllers/
COPY server/utils/ /var/www/html/api/utils/
COPY server/vendor/ /var/www/html/api/vendor/
COPY server/index.php /var/www/html/api/
COPY server/setup.php /var/www/html/api/
COPY server/.htaccess /var/www/html/api/
COPY server/Configuration-Docker.php /var/www/html/api/Configuration.php

#  Create database.
COPY 50-server.cnf /etc/mysql/mariadb.conf.d

#  Expose the ports.
#EXPOSE 3306

#  Run the servers.
WORKDIR /var/www/html
ENTRYPOINT ["bash", "/var/www/html/startup.sh"]
