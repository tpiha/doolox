Doolox
======

Doolox is an Open Source website builder and WordPress management tool written in Laravel PHP framework. It is created to help web design studios manage their WordPress projects and to help WordPress beginners with the installation and WordPress setup.

## Installation ##

```
git clone https://github.com/tpiha/doolox.git
```

Now setup app/config/database.php file and run this in the shell:

```
composer update
php artisan migrate:install
php artisan migrate
php artisan cpanel:user
```

You can now login on [http://localhost/doolox/](http://localhost/doolox/)