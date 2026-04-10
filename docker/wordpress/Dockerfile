FROM wordpress:6.8-php8.3-apache

RUN apt-get update \
	&& apt-get install -y --no-install-recommends curl default-mysql-client \
	&& curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
	&& chmod +x /usr/local/bin/wp \
	&& rm -rf /var/lib/apt/lists/*

COPY docker/wordpress/init-wordpress.sh /usr/local/bin/init-wordpress.sh

RUN chmod +x /usr/local/bin/init-wordpress.sh

ENTRYPOINT ["init-wordpress.sh"]
CMD ["apache2-foreground"]