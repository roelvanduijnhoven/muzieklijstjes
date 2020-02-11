FROM php:5.6-apache

# Use current user
#ENV UID=1000
#RUN \
    #groupadd docker_user && \
    #useradd docker_user -g docker_user -u ${UID} && \
    #mkdir /home/docker_user && \
    #chown docker_user:docker_user /home/docker_user

# PHP
RUN docker-php-ext-install mysql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ADD docker/php.ini $PHP_INI_DIR/conf.d/custom.ini

# XDebug
# RUN pecl install -o -f xdebug \
#  && rm -rf /tmp/pear \
#  && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini

#RUN echo 'include=/usr/local/etc/php-fpm/conf.d/*' >> /usr/local/etc/php-fpm.conf \
    #&& mkdir -p /usr/local/etc/php-fpm/conf.d/

ADD register /var/www/html