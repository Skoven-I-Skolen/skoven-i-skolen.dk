ARG PHP_VERSION="8.1"
FROM php:${PHP_VERSION}-fpm

# Run as root
USER root

# Use production config
#RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Define the workdir
WORKDIR /var/www

USER root

# Install dependencies
RUN apt-get update \
  && apt-get upgrade -y \
  && apt-get install -y --no-install-recommends \
  libbz2-dev \
  libfreetype6-dev \
  libgmp-dev \
  libjpeg-dev \
  libpng-dev \
  libpq-dev \
  libzip-dev \
  libjpeg62-turbo-dev \
  libz-dev \
  libmagickwand-dev \
  libmagickcore-dev \
  libicu-dev \
  libxml2-dev \
  libwebp-dev \
  libwebp6 \
  webp \
  libmagickwand-dev \
  git \
  curl \
  cron \
  imagemagick \
  msmtp \
  mariadb-client \
  redis-tools \
  sendmail-bin \
  sendmail \
  sensible-mda \
  sudo \
  vim \
  wget \
  zip \
  unzip \
  zlib1g-dev \
  libmemcached-dev

RUN cd /tmp && \
  wget https://deb.nodesource.com/setup_16.x && \
  chmod +x /tmp/setup_16.x && \
  ./setup_16.x && \
  apt install -y nodejs && \
  npm install -g grunt-cli postcss-cli --unsafe-perm=true

RUN pecl install -o -f \
  igbinary \
  imagick \
  redis \
  uploadprogress \
  memcached \
  xmlrpc
#  jsmin


RUN apt install software-properties-common -y && \
  add-apt-repository ppa:ondrej/php && \
  apt update && \
  apt -y upgrade

RUN apt install nginx -y && \
    apt install php8.1 && \
    apt install php8.1-fpm

COPY --from=ochinchina/supervisord:latest /usr/local/bin/supervisord /usr/local/bin/supervisord

# Configure the gd library
RUN docker-php-ext-configure gd \
  --enable-gd \
  --with-freetype \
  --with-webp \
  --with-jpeg
RUN docker-php-ext-configure opcache --enable-opcache
RUN docker-php-ext-configure calendar
RUN docker-php-ext-configure zip --with-zip

# Install required PHP extensions
RUN docker-php-ext-install -j$(nproc) \
  bcmath \
  bz2 \
  calendar \
  exif \
  gd \
  gettext \
  gmp \
  intl \
  opcache \
  pdo_mysql \
  pdo_pgsql \
  zip \
  pcntl \
  soap

RUN docker-php-ext-enable \
  igbinary \
  imagick \
  redis \
  uploadprogress \
  memcached \
  xmlrpc
#  jsmin

#Shell setup
RUN echo 'alias ll="ls -lah"' >> ~/.bashrc

ARG NEWRELIC_VERSION
RUN if [ "$NEWRELIC_VERSION" != "" ] ; then curl -L https://download.newrelic.com/php_agent/release/newrelic-php5-${NEWRELIC_VERSION}-linux.tar.gz | tar -C /tmp -zx && \
  export NR_INSTALL_USE_CP_NOT_LN=1 && \
  export NR_INSTALL_SILENT=0 && \
  /tmp/newrelic-php5-*/newrelic-install install && \
  rm -rf /tmp/newrelic-php5-* /tmp/nrinstall* ; fi

#COPY php-fpm/etc/php-fpm.ini /usr/local/etc/php/conf.d/zz-drupal.ini
#COPY php-fpm/etc/mail.ini /usr/local/etc/php/conf.d/zz-mail.ini
#COPY php-fpm/etc/php-fpm.conf /usr/local/etc/
#COPY php-fpm/entrypoint.sh /entrypoint.sh

COPY . ./
COPY .novi/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .novi/docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .novi/docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY .novi/docker/fpm/www.conf /etc/php/8.0/fpm/pool.d/www.conf

# Install source, run composer and install dependencies
# RUN composer install && \
#   apt clean && \
#   apt autoclean -y && \
#   apt autoremove -y && \
#   rm -rf /var/lib/{apt,dpkg,cache,log}/

# Start supervisor
ENTRYPOINT ["/usr/local/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
