#!/bin/bash
cp .env.example .env
sudo docker-compose up -d --build
sleep 5
sudo docker-compose exec database mysql -uroot -proot -e "create database seekator"
docker-compose exec php composer install
docker-compose exec php php artisan migrate --seed
docker-compose exec nodejs npm install
docker-compose exec nodejs npm run dev
docker-compose exec php php artisan websockets:serve
