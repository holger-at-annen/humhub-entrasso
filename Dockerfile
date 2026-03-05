FROM mriedmann/humhub:stable

RUN apk update --no-cache && \
    apk add --no-cache git && \
    git clone https://github.com/holger-at-annen/humhub-entrasso.git /var/www/localhost/htdocs/protected/modules/entrasso && \
    chown -R nginx:nginx /var/www/localhost/htdocs/protected/modules/entrasso && \
    chmod -R 755 /var/www/localhost/htdocs/protected/modules/entrasso && \
    rm -rf /var/cache/apk/*  # Clean up
