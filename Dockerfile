# PHP Images can be found at https://hub.docker.com/_/php/
FROM php:7.3-alpine

ARG BUILD_DATE
ARG VCS_REF
ARG VERSION
ARG HTTPS_SETTING
ARG IMAGE_VERSION
LABEL org.label-schema.build-date=$BUILD_DATE \
      org.label-schema.name="kester.pro" \
      org.label-schema.description="Docker Container for Kesters MariaDB K8s Demo" \
      org.label-schema.url="https://github.com/mariadb-kester/phpAppDocker" \
      org.label-schema.vcs-ref=$VCS_REF \
      org.label-schema.vcs-url="https://github.com/mariadb-kester/phpAppDocker" \
      org.label-schema.vendor="Kester Riley" \
      org.label-schema.version=$VERSION \
      org.label-schema.schema-version="1.0" \
      maintainer="Kester Riley <kesterriley@hotmail.com>" \
      architecture="AMD64/x86_64"

COPY /var/www /var/www

# Custom Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html
# Ensure PHP logs are captured by the container
ENV LOG_CHANNEL=stderr

RUN apk update \
    && apk upgrade --available && sync
# Concatenated RUN commands
RUN apk add --update gzip apache2 php7-apache2 php7-mbstring php7-session php7-json php7-pdo php7-openssl php7-tokenizer php7-pdo php7-pdo_mysql php-mysqli php7-xml php7-simplexml php7-curl curl-dev \
    &&  chown -R www-data:www-data /var/www/  \
    &&  chmod -R 750 /var/www/  \
    && mkdir -p /run/apache2 \
    && sed -i '/LoadModule rewrite_module/s/^#//g' /etc/apache2/httpd.conf \
    && sed -i '/LoadModule session_module/s/^#//g' /etc/apache2/httpd.conf \
    && sed -i '/LoadModule deflate_module modules\/mod_deflate.so/s/^#//g' /etc/apache2/httpd.conf \
    && sed -ri -e 's!/var/www/localhost/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/httpd.conf \
    && sed -i 's/AllowOverride\ None/AllowOverride\ All/g' /etc/apache2/httpd.conf \
    && sed -i 's/ServerTokens\ OS/ServerTokens\ Prod/g' /etc/apache2/httpd.conf \
    && sed -i 's/ServerSignature\ On/ServerSignature\ Off/g' /etc/apache2/httpd.conf \
    && echo "TraceEnable off" >> /etc/apache2/httpd.conf \
    && echo "Header always append X-Frame-Options SAMEORIGIN" >> /etc/apache2/httpd.conf \
    && echo "Header edit Set-Cookie ^(.*)$ $1;HttpOnly;Secure" >> /etc/apache2/httpd.conf \
    && docker-php-ext-configure curl \
    && docker-php-ext-install pdo_mysql mysqli \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"  \
    && sed -ri -e 's!;max_input_vars = 1000!max_input_vars = 9999!g' $PHP_INI_DIR/php.ini \
    && sed -ri -e 's!post_max_size = 8M!post_max_size = 20M!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!max_input_time = 60!max_input_time = 400!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!enable_dl = Off!enable_dl = On!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!expose_php = On!expose_php = Off!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!max_execution_time = 30!max_execution_time = 60!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!request_order = "GP"!;request_order = "GP"!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!serialize_precision = -1!serialize_precision = 100!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!short_open_tag = Off!short_open_tag = On!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 20M!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!;upload_tmp_dir =!upload_tmp_dir = /tmp/!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!variables_order = "GPCS"!variables_order = "EGPCS"!g' $PHP_INI_DIR/php.ini  \
    && sed -ri -e 's!zend.enable_gc = On!zend.enable_gc = Off!g' $PHP_INI_DIR/php.ini \
    && echo 'SetEnv HTTPS' ${HTTPS_SETTING} > /etc/apache2/conf.d/environment.conf \
    && echo 'SetEnv IMAGE_VERSION' ${IMAGE_VERSION} >> /etc/apache2/conf.d/environment.conf \
    && rm  -rf /tmp/* /var/cache/apk/* \
    && chown -R apache:apache /etc/apache2 \
    && chmod -R 700 /etc/apache2

   # INSTALL OPCACHE to help with performance.
   ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"
   ADD opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

  # Launch the httpd in foreground
  CMD rm -rf /run/apache2/* || true && /usr/sbin/httpd -DFOREGROUND
