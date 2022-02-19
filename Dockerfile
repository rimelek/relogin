FROM itsziget/php:5.6-fpm

RUN docker-php-ext-configure mysql \
 && docker-php-ext-install mysql \
 && docker-php-ext-enable mysql

RUN apt-get update \
 && apt-get install --no-install-recommends -y \
      autoconf g++ make \
 && yes | pecl install xdebug-2.5.5

RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.max_nesting_level=1500" >> /usr/local/etc/php/conf.d/xdebug.ini 

RUN echo "php_admin_value[date.timezone] = UTC" >> /usr/local/etc/php-fpm.d/zz-docker-custom.conf
