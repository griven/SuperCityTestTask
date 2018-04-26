FROM composer as php_redis

# установка redis extension
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/4.0.2.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext/ \
    && mv phpredis-4.0.2 /usr/src/php/ext/redis \
    && cd /usr/src/php/ext/redis \
    && docker-php-ext-install redis

