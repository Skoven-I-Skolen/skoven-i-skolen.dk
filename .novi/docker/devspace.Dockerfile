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
COPY .novi/docker/supervisor/devspace.supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .novi/docker/nginx/devspace.nginx.conf /etc/nginx/nginx.conf
COPY .novi/docker/nginx/devspace.default.conf /etc/nginx/conf.d/default.conf
COPY .novi/docker/fpm/devspace.www.conf /etc/php/8.0/fpm/pool.d/www.conf

# Install source, run composer and install dependencies
RUN composer install && \
  apt clean && \
  apt autoclean -y && \
  apt autoremove -y && \
  rm -rf /var/lib/{apt,dpkg,cache,log}/ && \
  # Make drush globally available in the CLI
  touch /usr/local/bin/drush && \
  echo "#!/bin/bash" > /usr/local/bin/drush && \
  echo "/usr/bin/php8.0 /var/www/vendor/bin/drush \$@" >> /usr/local/bin/drush && \
  chmod +x /usr/local/bin/drush

# Start supervisor
ENTRYPOINT ["/usr/local/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
