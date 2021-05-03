FROM php:8.0.2-apache
EXPOSE 80
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
RUN a2enmod rewrite
RUN apt-get update \
  && apt-get install -y libzip-dev git wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN docker-php-ext-install pdo mysqli pdo_mysql zip;
RUN wget https://getcomposer.org/download/2.0.9/composer.phar \ 
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
COPY . /var/www
WORKDIR /var/www
RUN chmod +x /entrypoint.sh
RUN chmod -R 777 var/log/
RUN chmod -R 777 var/cache/
CMD ["apache2-foreground"]
ENTRYPOINT ["/entrypoint.sh"]
RUN usermod -u 1000 www-data