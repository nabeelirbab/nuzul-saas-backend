FROM nginx:stable-alpine

RUN apk add --no-cache tzdata
ENV TZ Asia/Riyadh
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY nginx/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/public

COPY public/ .
