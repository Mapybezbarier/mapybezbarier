FROM nginx:1.15-alpine

# Install nginx
RUN apk add --update nginx \
    && echo "upstream php-upstream { server php-upstream:9000; }" > /etc/nginx/conf.d/upstream.conf

# Setup vhost
ADD etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# Expose ports
EXPOSE 80
