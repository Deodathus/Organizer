FROM php:8.1-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip librabbitmq-dev \
    && pecl install apcu amqp xdebug rabbitmq \
    && docker-php-ext-install intl opcache pdo pdo_mysql sockets zip \
    && docker-php-ext-enable apcu amqp \
    && docker-php-ext-configure zip

RUN apt-get update && apt-get install -y supervisor
RUN mkdir -p /var/run/sshd /var/log/supervisor /var/run/supervisor

COPY supervisord.conf /etc/supervisor/supervisord.conf

COPY entrypoint-base.sh /usr/local/bin/entrypoint-base.sh

RUN chmod +x /usr/local/bin/entrypoint-base.sh

COPY xdebug.ini "${PHP_INI_DIR}/conf.d"

WORKDIR /var/www/organizer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli
RUN git config --global user.email "bslexmail@gmail.com" \
    && git config --global user.name "Bohdan Mykhalevych"

ENTRYPOINT ["/bin/sh", "/usr/local/bin/entrypoint-base.sh"]