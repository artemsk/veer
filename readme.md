## Veer Engine 


[![Scrutinizer](https://img.shields.io/scrutinizer/g/artemsk/veer.svg)](https://scrutinizer-ci.com/g/artemsk/veer/)
[![GitHub release](https://img.shields.io/github/release/artemsk/veer.svg)]()
[![Packagist](https://img.shields.io/packagist/l/artemsk/veer.svg)]()
[![Tea](https://img.shields.io/badge/cups%20of%20tea-351-ff69b4.svg)]()

## Requirements & Installation

Veer uses Composer to manage dependencies. Also there are several system requirements:
* PHP >= 5.4
* Mcrypt PHP Extension
* Mbstring PHP Extension
* JSON PHP Extension
* MySQL or SQLite (**coming soon**)

- Clone repository `$ git clone https://github.com/artemsk/veer.git ./` or use Composer to install Veer `$ composer create-project artemsk/veer ./`

If you don't have Composer install it with `$ php -r "readfile('https://getcomposer.org/installer');" | php`

- Install all dependencies `$ composer update` (or `$ php composer.phar update`)

- Copy and rename main configuration file — *.env.example* to *.env*. Set database parameters in it (others are optional):
```
DB_HOST=<localhost or url>
DB_DATABASE=<your database name>
DB_USERNAME=<database username>
DB_PASSWORD=<database password>
```

- Set permissions for these folders: **storage**, **vendor**.

- Run these commands:
```
$ php artisan config:cache
$ php artisan route:cache
```

- Run main installation command with your initial main url. You will be asked to set administrator login and password.
```
$ php artisan veer:install <url> --migrate
```

- Follow instructions you will see.

### License

Veer is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).