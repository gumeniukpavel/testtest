FROM php:7.4-fpm

RUN mkdir -p /usr/share/man/man1

RUN apt-get update --fix-missing
RUN apt-get install -y libpq-dev \
    libpcre3-dev \
    libzip-dev \
    libc-client-dev \
    libkrb5-dev \
    libxml2-dev \
    git \
    curl \
    libmcrypt-dev \
    zlib1g-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libsodium-dev \
    pkg-config \
    libbz2-dev \
    mariadb-client \
    pdftk \
    gettext \
    supervisor \
    mc \
    nano

RUN apt-get install -y --no-install-recommends apt-utils

# Install nginx
RUN apt-get install -y nginx
RUN rm /etc/nginx/sites-available/default
COPY devops/common/nginx/backend.conf /etc/nginx/sites-available/default

# Install node
RUN apt-get remove -y nodejs
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs

#Copy schedler job
COPY devops/common/supervisor/run-scheduler.sh /user/local/bin/run-scheduler.sh
RUN chmod 777 /user/local/bin/run-scheduler.sh

# Add SystemD service configuration
COPY devops/common/supervisor/queue.conf /etc/supervisor/conf.d/queue.conf
COPY devops/common/supervisor/scheduler.conf /etc/supervisor/conf.d/scheduler.conf

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN pecl install mcrypt-1.0.3 libzip
RUN docker-php-ext-install -j$(nproc) iconv
RUN docker-php-ext-enable mcrypt
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install imap pdo pdo_mysql bcmath mbstring zip gd opcache sodium soap

# install redis extension
RUN pecl install -o -f redis &&  rm -rf /tmp/pear
RUN docker-php-ext-enable redis

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN touch /tmp/xdebug_remote.log
RUN chmod 777 /tmp/xdebug_remote.log

COPY devops/common/uploads.ini         /usr/local/etc/php/conf.d/

COPY devops/local/99-xdebug.ini.tpl   /usr/local/etc/php/conf.d/
COPY devops/local/boot.sh             /usr/local/bin/php_boot

CMD ["sh", "/usr/local/bin/php_boot"]
