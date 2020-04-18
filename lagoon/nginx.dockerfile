FROM amazeeio/nginx
COPY lagoon/nginx.conf /etc/nginx/conf.d/app.conf

RUN fix-permissions /etc/nginx/conf.d/app.conf

COPY . /app
