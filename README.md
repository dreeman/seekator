![Seekator](https://raw.githubusercontent.com/dreeman/seekator/main/image.png)

## Seekator 0.1.0

Seekator is a simple online tool to trim and save/download video from YouTube.

### Setup

    # Prepare .env-file in project's root directory
    $ copy .env.example .env

    # Run docker-compose in the project's root folder
    $ docker-compose up -d --build

    # Install composer packages
    $ docker-compose exec php composer install

    # Run migrates with seeds
    $ docker-compose exec php php artisan migrate --seed

    # Install npm packages
    $ docker-compose exec nodejs npm install

    # Run nodejs build (or watch) command
    $ docker-compose exec nodejs npm run build

    # Start Websockets server
    $ docker-compose exec php php artisan websockets:serve

    # Open application in your browser 
    http://seekator.localhost

### Credentials
    
    Login:    seekator@seekator.com
    Password: seekator

### Used technologies

- PHP 7.4
- Laravel 8
- VueJS 2
- Websockets (Pusher)
- Docker
- youtube-dl (with Python) and FFMPEG in Docker container
