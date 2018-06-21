FROM php:7.1.5-fpm
ENV http_proxy 'http://proxy.mpt.es:8080'
ENV https_proxy 'https://proxy.mpt.es:8080'
ENV ftp 'ftp://proxy.mpt.es:8080'
RUN apt-get update && apt-get install -y git libcurl4-gnutls-dev zlib1g-dev libicu-dev g++ libxml2-dev libpq-dev \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql intl curl json opcache xml \
 && apt-get autoremove && apt-get autoclean \
 && rm -rf /var/lib/apt/lists/*
COPY . /var/www/html/
COPY docker/php/conf/30-custom.ini /usr/local/etc/php/
EXPOSE 80

  #FROM php:7.0-fpm
#RUN apt-get update && apt-get install -y \
#		libfreetype6-dev \
#		libjpeg62-turbo-dev \
#		libmcrypt-dev \
#		libpng12-dev \
#	&& docker-php-ext-install -j$(nproc) iconv mcrypt \
#	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
#	&& docker-php-ext-install -j$(nproc) gd
#	&& docker-php-ext-install mysqli pdo_pgsql pdo_mysql

