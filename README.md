## Laravel App

🎯 Overview

Laravel App - is a standard laravel setup application like a starterkit for rapid api development.

## Install as a Starter Kit

Create a new project from this starter kit:

```sh
# Via Packagist (public) — composer create-project abdulsalamamtech/laravelapp my-app

# Via GitHub VCS (works with private repos / no Packagist)
composer create-project --repository='{"type":"vcs","url":"git@github.com:abdulsalamamtech/laravelapp.git"}' abdulsalamamtech/laravelapp my-app
```

Then set up the new project:

```sh
cd my-app
composer setup                # installs deps, copies .env, generates key, migrates, builds assets
php artisan serve
```

See [docs/version.md](docs/version.md) for versioning and packaging details.

## Application Setup

```sh
    git clone ...
    cd app
    composer install
    npm install
    php artisan migrate
    php artisan migrate && php artisan db:seed --class=AdminSeeder
    php artisan serve
```

## GitHub Commands

```sh
    git commit -m"feat (model): Add fillable column"
    git commit -m"feat (user): add users feature"
    git commit -m"feat (route): add users endpoint"
    git commit -m"feat (test): add user feature test"
    git commit -m"feat (docs): add auth api documentation"
    git commit -m"fix (payment): handle declined card transactions"
    git commit -m"docs (readme): update installation instructions"
    git commit -m"test (auth test): add auth feature test"
    git commit -m"refactor (auth): improving code quality"
    git commit -m"chore (package): updating dependencies to latest versions"
    git commit -m"ci (github): add automated deployment workflow"

```

## pull request description

- summary: fix the api configuration exception error
- Summary: Add auth features for admin.

## GitHub branch

- staging
- production

## Documentation Route

https://domain.com/docs/api

## Api response

    HTTP/1.1 201 Created
    Host: 127.0.0.1:8000
    Connection: close
    X-Powered-By: PHP/8.3.6
    Cache-Control: no-cache, private
    Date: Sat, 22 Nov 2025 13:58:51 GMT
    Content-Type: application/json
    Access-Control-Allow-Origin: \*

```sh


{
  "success": true,
  "message": "User created successfully",
  "status": 201,
  "data": {
    "user": {
      "email": "abdulsalamamtech@gmail.com",
      "id": "019aabdc-34ad-7361-bc16-e3dd4b9330d4",
      "updated_at": "2025-11-22T13:58:46.000000Z",
      "created_at": "2025-11-22T13:58:46.000000Z"
    },
  }
  "token": "1|r8rLhqBKmXbOFC7Z2WOAE7ioMR0LStNDUQYcIzLK37205580"
}

```

## Using latest Gemini model

Last Review: 22 July 2026

Docs: https://ai.google.dev/gemini-api/docs/latest-model

Gemini 3.6 Flash (gemini-3.6-flash) and Gemini 3.5 Flash-Lite (gemini-3.5-flash-lite) are generally available (GA) and ready for production use.

```sh

model="gemini-3.5-flash-lite"

```

Model: Gemini 3.5 Flash-Lite gemini-3.5-flash-lite

Use case: Autonomous subagent execution, high-volume data analysis and document extraction, structured JSON parsing

Model: Gemini 3.6 Flash gemini-3.6-flash

Use case: Code generation, spatial/multimodal reasoning, multi-step agentic workflows

## External packages

- Role and Permission [spatie permission](https://spatie.be/docs/laravel-permission)
- Filament Sheield [filament shield team](https://filamentphp.com/plugins/bezhansalleh-shield)
- Media Files [spatie media library](https://spatie.be/docs/laravel-medialibrary)
- Query Buider [spatie laravel query builder](https://spatie.be/docs/laravel-query-builder/v6/introduction)
- Laravel AI Package [Laragent](https://docs.laragent.ai/quickstart)
- Backup Database [spatie laravel backup](https://spatie.be/docs/laravel-backup/v9/introduction)
- Api Documentation [scample openapi](https://scramble.dedoc.co/)
- Advance Excel Lib [maatwebsite/excel](https://docs.laravel-excel.com/3.1/getting-started/installation.html)
- Excel library [phpoffice phpspreadsheet](https://phpspreadsheet.readthedocs.io/en/latest/)
- Filament Activity Log Manager
- Filament Laravel Backup [spatie laravel backup](https://filamentphp.com/plugins/shuvroroy-spatie-laravel-backup)

1. [ Ali Harb Filament Activity Log with timeline](https://filamentphp.com/plugins/alizharb-activity-log)
2. [Rmsramos Activity Log Advance with restore](https://filamentphp.com/plugins/rmsramos-activitylog)

## Application pattern

The Controller → Service → Repository (CSR) Pattern

It focuses on separation of Concerns Large-scale enterprise systems with complex data requirements: [view example](./docs/pattern.md).

- Controller: only handle request and response
- Service Layer: contain business logic
- Repository Layer: abstract data access

## Security check and standards

```sh

    composer require --dev phpstan/phpstan
    ./vendor/bin/phpstan analyse

    composer require spatie/laravel-backup
    php artisan backup:run
```

## production

A. Option

-- Enable the Functions in Hostinger hPanel (Recommended)
The cleanest fix is to temporarily unblock the functions so the Artisan command can run successfully.

1. Log in to your Hostinger hPanel.
2. Search for and open `PHP Configuration.`
3. Select the PHP `Options tab.`
4. Find the `disable_functions` field.
5. Remove `symlink` and `exec` from the comma-separated text list.
6. Click `Save` at the bottom of the page.
7. Return to your terminal and re-run your command:

```php
  php artisan storage:link
```

- Note: You can add symlink and exec back to the disabled list afterward if you want to maintain maximum server security.

B. Option

```php
  use Illuminate\Support\Facades\Artisan;

  Route::get('/link-my-storage', function () {
      Artisan::call('storage:link');
      return 'Storage symlink created successfully!';
  });

```

## update the timezone

```sh
# .env
APP_TIMEZONE=Africa/Lagos

# config/app.php
'timezone' => env('APP_TIMEZONE', 'UTC'),

# sh
php artisan config:cache

```

---

To remove a file from your most recent commit and immediately give that removed file its own separate commit with a different message, you can undo the commit softly, split the files, and recommit them.

```sh
# 1. Undo the last commit but keep all your file changes staged
git reset --soft HEAD~1

# 2. Unstage the specific file you want to separate
git restore --staged <path/to/your/file>

# 3. Commit the remaining staged files with your original or a new message
git commit -m "Original commit message without the removed file"

# 4. Stage the file you just removed
git add <path/to/your/file>

# 5. Commit that specific file with its own new commit message
git commit -m "Your different commit message for the removed file"


```

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
