FROM composer

RUN apk --update add autoconf g++ make \
    # igbinary нужен для redis
    && pecl install -o -f igbinary \
    && echo "extension=igbinary.so" > /usr/local/etc/php/conf.d/igbinary.ini \
    # установка расширения redis
    && pecl install -o -f redis \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini \
    # установка расширения mongo
    && pecl install -o -f mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/ext-mongo.ini
