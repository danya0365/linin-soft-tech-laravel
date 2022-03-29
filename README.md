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

Install dependency
```sail yarn```

Add new package
```sail yarn add {packageName}```

Build Js/css 
```sail yarn dev```

### DB Seeder

```sail php artisan migrate:fresh --seed```

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

### ทุกครั้งที่มีการเพิ่ม Lib ด้วย NodeJS

ต้องทำการติดตั้งและ Generate css และ js ไปที่ public folder ด้วยคำสั่ง

```yarn && yarn dev```

### Generate Enum

สร้าง enum UserType
```
php artisan make:enum UserType
```

### Generate Event & Listener
```
sail php artisan make:event EmployeeOperationLogCreated
sail php artisan make:listener CalculateUserWorkingTimeNotification  --event=EmployeeOperationLogCreated
```

### CronJob Schedule

add new command
```sail php artisan make:command DaillyReportCron --command=dailyReport:cron```

open ```app/Console/Commands/DaillyReportCron.php```

search for ```function handle()```

add below code 

```
use Illuminate\Support\Facades\Log;
...

function handle() {
    Log::info("Cron is working fine!");
}
```

open ```app/Console/Kernel.php```

add below code in ```function schedule(Schedule $schedule)```

```$schedule->command(DaillyReportCron::class)->daily();```

test to force run schedule

```sail php artisan schedule:run```

***only for local server to force cronjob running
```sail php artisan schedule:work```

### Set up your server to run crontab every second
At last you can manage this command on scheduling task, you have to add a single entry to your server’s crontab file:
```* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1```

## Executing Commands

When using Laravel Sail, your application is executing within a Docker container and is isolated from your local computer. However, Sail provides a convenient way to run various commands against your application such as arbitrary PHP commands, Artisan commands, Composer commands, and Node / NPM commands.

When reading the Laravel documentation, you will often see references to Composer, Artisan, and Node / NPM commands that do not reference Sail. Those examples assume that these tools are installed on your local computer. If you are using Sail for your local Laravel development environment, you should execute those commands using Sail:

### Running Artisan commands locally...
```php artisan queue:work```

### Running Artisan commands within Laravel Sail...
```./vendor/bin/sail artisan queue:work```

### Show PHP version on your sail
```./vendor/bin/sail php --version```

# Semantic Commit Messages

See how a minor change to your commit message style can make you a better programmer.

Format: `<type>(<scope>): <subject>`

`<scope>` is optional

## Example

```
feat: add hat wobble
^--^  ^------------^
|     |
|     +-> Summary in present tense.
|
+-------> Type: chore, docs, feat, fix, refactor, style, or test.
```

More Examples:

- `feat`: (new feature for the user, not a new feature for build script)
- `fix`: (bug fix for the user, not a fix to a build script)
- `docs`: (changes to the documentation)
- `style`: (formatting, missing semi colons, etc; no production code change)
- `refactor`: (refactoring production code, eg. renaming a variable)
- `test`: (adding missing tests, refactoring tests; no production code change)
- `chore`: (updating grunt tasks etc; no production code change)

References:

- https://www.conventionalcommits.org/
- https://seesparkbox.com/foundry/semantic_commit_messages
- http://karma-runner.github.io/1.0/dev/git-commit-msg.html

### Install tailwind with sass
[Install Tailwind CSS & SASS with Laravel Mix (2022)](https://ralphjsmit.com/tailwind-sass-laravel)
[Tailwind Documentation](https://tailwindcss.com/docs/installation)