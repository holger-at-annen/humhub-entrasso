FROM mriedmann/humhub:latest

RUN apk add --no-cache git \
    && git clone https://github.com/holger-at-annen/humhub-entrasso.git /var/www/localhost/htdocs/protected/modules/entrasso \
    && chown -R www-data:www-data /var/www/localhost/htdocs/protected/modules/entrasso
