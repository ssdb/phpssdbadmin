FROM richarvey/nginx-php-fpm:latest

ADD . /var/www/html/

RUN apk add openssl

ADD ./docker/config.php /var/www/html/app/config/config.php
ADD ./docker/initialize.sh /var/www/html/scripts/initialize.sh
ADD ./docker/nginx-site.conf /var/www/html/conf/nginx/nginx-site.conf

ENV RUN_SCRIPTS 1