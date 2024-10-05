#!/bin/bash

echo "Backend env copying ..."
cp news-aggregator-be/.env.example news-aggregator-be/.env

echo "Stoping all container ..."
docker-compose down --remove-orphans

echo "Frontend env copying ..."
cp news-aggregator-fe/.env.example news-aggregator-fe/.env

echo "New docker-compose build started ..."
echo "Please wait for a while to build with no cache ...."
echo "Run container with detach mode ...."
docker-compose up --build -d
echo "Generating new backend app key"
docker exec -it backend-container php artisan key:generate

# echo "Backend config clearing ..."
docker exec -it backend-container php artisan config:clear
# echo "Backend cache clearing ..."
docker exec -it backend-container php artisan cache:clear
echo "Waiting for MySQL db container ready ......\n"
sleep 15
echo "Migrating backend schema"
docker exec -it backend-container php artisan migrate:fresh
echo "Generating demo seed data"
docker exec -it backend-container php artisan db:seed

# echo "Running lint on frontend container"
# docker exec -it frontend-container npm run lint

echo "Please visit http://localhost:7890 to visit the app"
