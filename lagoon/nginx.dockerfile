FROM uselagoon/nginx:latest
COPY . /app

# Rsync for file copying.
RUN apk add --no-cache rsync

# Mangle Nginx to run on port 3000 so we can emulate `nginx-persistant`.
COPY lagoon/static-files.conf /etc/nginx/conf.d/app.conf
EXPOSE 3000
ENV LAGOON_LOCALDEV_HTTP_PORT=3000