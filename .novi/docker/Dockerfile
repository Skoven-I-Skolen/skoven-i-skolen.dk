ARG PHPVERSION="8.0"

FROM karbowiak/docker-php:php${PHPVERSION}-nginx-fpm

# Run as root
USER root

# Setup container
RUN apt update && \
  cd /tmp && \
  wget https://deb.nodesource.com/setup_16.x && \
  chmod +x /tmp/setup_16.x && \
  ./setup_16.x && \
  apt install -y nodejs && \
  npm install -g grunt-cli postcss-cli --unsafe-perm=true

# Define the workdir
WORKDIR /var/www


ARG PHPVERSION="8.0"

COPY . ./
COPY .novi/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .novi/docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .novi/docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY .novi/docker/fpm/www.conf /etc/php/8.0/fpm/pool.d/www.conf

###
## npm etc.
###


# Install source, run composer and install dependencies
RUN composer install && \
  apt clean && \
  apt autoclean -y && \
  apt autoremove -y && \
  rm -rf /var/lib/{apt,dpkg,cache,log}/

# Start supervisor
ENTRYPOINT ["/usr/local/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
