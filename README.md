## Welcome to Laravel API

---

## Development setup steps (MacOS and Linux) via Terminal

> This is a Laravel Application, make sure to visit https://laravel.com/docs/8.x#server-requirements before proceeding into the Steps.


Step 1: Clone the repo and checkout into staging

```sh
git clone https://YOUR_USERNAME@github.com/binnasser/laravel-api.git
cd laravel-api
git checkout staging
```

Step 2: Do the require installation via Composer

```sh
composer install
```

Step 3: Create the environment file

```sh
cp .env.example .env
```

Step 4: Generate Laravel app key

```sh
php artisan key:gen
```

Step 5: Run the test commend to make sure your application is running.

```sh
php artisan test
```

Step 6: To start the application in the development server

```sh
php artisan serve
```

## Helpful commands
---

##Code Syntax Check

To check the code syntax (using phplint)

```sh
composer phplint #./vendor/bin/phplint . --exclude=vendor
```
## PHP Coding Standards Check

To check PHP Coding Standards (using PHP CS Fixer)

```sh
composer php-cs-diff #./vendor/bin/php-cs-fixer fix --diff --dry-run
```

To fix any diffs

```sh
composer php-cs-fix #./vendor/bin/php-cs-fixer fix
```
