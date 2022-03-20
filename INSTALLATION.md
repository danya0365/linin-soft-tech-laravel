# Installation 

install docker-desktop on your mac

## Alias sail in your bash_profile

```alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'```

the you can run
```./vendor/bin/sail up```

or
```sail up```

## start sail

### start on normally
```./vendor/bin/sail up```

### start in background
```./vendor/bin/sail up -d```

Once the application's Docker containers have been started, you can access the application in your web browser at: http://localhost.

[Full Laravel Sail](https://laravel.com/docs/9.x/sail)

### stop sail

```./vendor/bin/sail stop```

### Execute NPM

```sail yarn```

### Database Management
[PhpMyAdmin](http://localhost:8081/)

### Generate CRUD 
[awais-vteams/laravel-crud-generator](https://github.com/awais-vteams/laravel-crud-generator)

Add migration file to create `table` first
```sail php artisan make:migration create_banks_table```

Then generate CRUD
```sail php artisan make:crud banks```

Add route.php
```Route::resource('banks', 'BankController');```


## Executing Commands

When using Laravel Sail, your application is executing within a Docker container and is isolated from your local computer. However, Sail provides a convenient way to run various commands against your application such as arbitrary PHP commands, Artisan commands, Composer commands, and Node / NPM commands.

When reading the Laravel documentation, you will often see references to Composer, Artisan, and Node / NPM commands that do not reference Sail. Those examples assume that these tools are installed on your local computer. If you are using Sail for your local Laravel development environment, you should execute those commands using Sail:

### Running Artisan commands locally...
```php artisan queue:work```

### Running Artisan commands within Laravel Sail...
```./vendor/bin/sail artisan queue:work```

### Show PHP version on your sail
```./vendor/bin/sail php --version```