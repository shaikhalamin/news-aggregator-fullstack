#!/bin/bash

echo "Backend env copying ..."
cp news-aggregator-be/.env.example news-aggregator-be/.env

echo "Stoping all container ..."
docker-compose down -v --remove-orphans
echo "Frontend env copying ..."
cp news-aggregator-fe/.env.example news-aggregator-fe/.env

echo "New docker-compose build started ..."
echo "Please wait for a while to build with no cache ...."
echo "Run container with detach mode ...."
#docker-compose up --build -d
docker-compose build --no-cache --pull
docker-compose up -d

echo "Waiting for MySQL db container ready ......\n"
sleep 20

docker exec -it backend-container composer install

echo "Migrating backend schema"
docker exec -it backend-container php artisan migrate:fresh

docker exec -it backend-container php artisan key:generate
# echo "Backend config cache clearing ..."
docker exec -it backend-container php artisan cache:clear
docker exec -it backend-container php artisan config:clear

echo "Please click http://localhost:7890 to visit the app"
# docker exec -it backend-container php artisan queue:work

# docker exec -it backend-container mkdir -p /etc/supervisor/logs

# docker exec -it backend-container /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf




# Fetch news feed
# docker exec -it backend-container php artisan newsfeed:fetch

# echo "Running lint on frontend container"
# docker exec -it frontend-container npm i dompurify


