![Seekator](https://github.com/dreeman/seekator/blob/main/image.png?raw=true)

## Seekator 0.1.0

Seekator is a simple online tool to trim and save/download video from YouTube.

### Setup

    # Prepare .env-file in the project's root folder
    $ copy .env.example .env

    # Run docker-compose in the project's root folder
    $ docker-compose up -d --build

    # Run migrates with seeds
    $ docker-compose exec php artisan migrate --seed

    # Start Websockets server
    $ docker-compose exec php artisan websockets:serve

    # Run node watch (or build) command
    $ docker-compose exec nodejs npm run watch

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
