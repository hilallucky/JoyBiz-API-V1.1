# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/lumen)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Create Project
composer create-project --prefer-dist laravel/lumen JoyBiz-API-V1.0

Install full artisan
composer require flipbox/lumen-generator

Install JWT-Auth
composer require tymon/jwt-auth "1.*"
or
composer require tymon/jwt-auth:dev-develop --prefer-source

Set CORS in App\Http\Middleware\CorsMiddleware

Create unit test (TDD)
php artisan make:test EndpointTest

Run unit test
phpunit tests/EndpointTest.php

php artisan make:test UserRegistrationTest
phpunit tests/UserRegistrationTest.php

php artisan make:test UserAuthTest
phpunit tests/UserAuthTest.php

Create file config auth ( config/auth.php )
Modificate method handle() in file app/Http/Middleware/Authenticate.php to format response ‘Unauthorized 401’

Set db connection in .env

Create JWT secret key
php artisan key:generate

php artisan make:migration create_users_table

php artisan make:controller AuthController

php artisan migrate:fresh --seed

phpunit tests/UserRegistrationTest.php

phpunit tests/UserAuthTest.php

php artisan make:test MovieCRUDTest

phpunit tests/MovieCRUDTest.php

php artisan make:model Movie -a

php artisan migrate:fresh --seed

phpunit tests/MovieCRUDTest.php

Install Swagger
composer require "darkaonline/swagger-lume:5.6.*"

Edit bootstrap/app.php file
$app->configure('swagger-lume');
$app->register(\SwaggerLume\ServiceProvider::class);

Copy files from \vendor\swagger-api\swagger-ui\dist to publi\swagger-ui

Publish configuration file for swagger-lume
php artisan swagger-lume:publish
php artisan swagger-lume:generate

Open http://{url}:{port}/api/documentation

Show composer list
composer require thedevsaddam/lumen-route-list

Uses
Run php artisan route:list to display the route list
Inorder to filter routes use php artisan route:list --filter=tableHeaderName:searchKeyword
To display in reverse order use --reverse
Filtering example given below:

php artisan route:list --filter=method:post
#The above example will filter all the routes with post method#
or
php artisan route:list --filter=name:user
#The above example will filter all the routes which name contains *user* keyword#
or to display in reverse order use
php artisan route:list --filter=name:user --reverse

Docker
docker run -p 5432:5432 -e POSTGRES_USER=postgres -e POSTGRES_PASSWORD=hilal12 -d postgres
docker run --name redis -d -p 6379:6379 redis redis-server --requirepass "redis_hilal123"
redis-cli -h 127.0.0.1 -p 6379 -a 'redis_hilal123'

Git
https://www.theserverside.com/blog/Coffee-Talk-Java-News-Stories-and-Opinions/How-to-push-an-existing-project-to-GitHub

Redis
composer require predis/predis
php artisan make:controller RedisTestController
php artisan config:clear
composer dump-autoload

