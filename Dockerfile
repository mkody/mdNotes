FROM docker.io/library/alpine:3.18
WORKDIR /app
RUN apk add --no-cache php82
COPY . /app

EXPOSE 8080
CMD [ "php", "-S", "0.0.0.0:8080", "routing.php" ]