## Backend

    Set up working environment, version control and libraries.
    Authentication workflow
    OTP and mail workflow

# Tech Stack

- https://docs.google.com/document/d/1s1vj6HzV6jUJyIp5ikVMcUlyWamblQvaBHZiIXdETwk/edit?tab=t.0


# More checklist updates

- Go Live (V1) - coming soon
- Api fail, timeout, logs, database backup schedule, rate limiting
- Privacy policy, terms & condition, The General Data Protection Regulation (GDPR)
- Feedback form
- Contact form (email)
- Pricing page
- Analytics on frontend (Google/PostHug/Plausible) - FE
- Error monitoring (Sentry/LogRocket/Grafana) - BE
  https://docs.sentry.io/platforms/php/guides/laravel/
  https://laravel-news.com/sentry-adds-logs-support-for-laravel-apps
  https://nightwatch.laravel.com/pricing - 0/M 300k events set limit to $0/M
- Page speed less than 2 second
- What's coming next page

## Track user login and devices

Installation: https://rappasoft.com/docs/laravel-authentication-log/v6/start/installation
Config: https://rappasoft.com/docs/laravel-authentication-log/v6/start/configuration
composer require rappasoft/laravel-authentication-log
class User {
use AuthenticationLoggable;
}
Location: composer require torann/geoip
Docs: (1k Requests/day)https://lyften.com/projects/laravel-geoip/doc/services.html
Docs: (45Req/min - 64800/Day) https://members.ip-api.com/#pricings

## User activity log

composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate

use Spatie\Activitylog\Models\Activity;

```php
activity()
->causedBy(auth()->user()) // who
->performedOn($post) // what
->withProperties(['ip' => request()->ip()]) // extra data
->withProperties(['customProperty' => 'customValue'])
->log('updated post'); // description

$lastLoggedActivity = Activity::all()->last();

$lastLoggedActivity->subject; //returns an instance of an eloquent model
$lastLoggedActivity->causer; //returns an instance of your user model
$lastLoggedActivity->getProperty('customProperty'); //returns 'customValue'
$lastLoggedActivity->description; //returns 'Look mum, I logged something'

https://filamentphp.com/plugins/daniel-green-logger
https://filamentphp.com/plugins/alizharb-activity-log


```

```php
# Basic Retrieval via Controller

use Spatie\Activitylog\Models\Activity;
use App\Models\Company;
use App\Models\User;

public function getCompanyActivity(Company $company)
{
    // Get all IDs for users in this company
    $userIds = $company->users()->pluck('id');

    // Retrieve activity logs caused by these users
    $activities = Activity::where('causer_type', User::class)
        ->whereIn('causer_id', $userIds)
        ->latest()
        ->paginate(20);

    return view('company.activity', compact('activities'));
}

  $company = Company::find(50);
  $logs = Activity::whereIn('causer_id', function ($query) use ($company) {
      $query->select('user_id')
          ->from('company_employees')
          ->where('company_id', $company->id);
  })->get();
```

## Laravel Pint Code Style & Formatting Fixers

Usage: Run ./vendor/bin/pint to fix your entire project.
Tip: Use the --dirty flag to only fix files with uncommitted changes.

./vendor/bin/pint --test
./vendor/bin/pint --dirty
./vendor/bin/pint app/Models/

## JOBS & QUEUE

Supervisor Configuration

- How to:
  https://dev.to/elsayed85/mastering-laravel-queues-a-complete-guide-to-background-job-processing-4lbg
- Link Filament Job Monitoring Package:
  https://madewithlaravel.com/filament-jobs-monitor
  https://filamentphp.com/plugins/croustibat-jobs-monitor

```sh
# The --max-jobs option may be used to instruct the worker to process the given number of jobs and then exit.
workers are automatically restarted after processing a given number of jobs, releasing any memory they may have accumulated:
php artisan queue:work --max-jobs=1000

# Process jobs for one hour and then exit...
# More checklist updates

- Go Live (V1) - coming soon
- Api fail, timeout, logs, database backup schedule, rate limiting
- Privacy policy, terms & condition, The General Data Protection Regulation (GDPR)
- Feedback form
- Contact form (email)
- Pricing page
- Analytics on frontend (Google/PostHug/Plausible) - FE
- Error monitoring (Sentry/LogRocket/Grafana) - BE
  https://docs.sentry.io/platforms/php/guides/laravel/
  https://laravel-news.com/sentry-adds-logs-support-for-laravel-apps
  https://nightwatch.laravel.com/pricing - 0/M 300k events set limit to $0/M
- Page speed less than 2 second
- What's coming next page

## Track user login and devices

Installation: https://rappasoft.com/docs/laravel-authentication-log/v6/start/installation
Config: https://rappasoft.com/docs/laravel-authentication-log/v6/start/configuration
composer require rappasoft/laravel-authentication-log
class User {
use AuthenticationLoggable;
}
Location: composer require torann/geoip
Docs: (1k Requests/day)https://lyften.com/projects/laravel-geoip/doc/services.html
Docs: (45Req/min - 64800/Day) https://members.ip-api.com/#pricings

## User activity log

composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate

use Spatie\Activitylog\Models\Activity;

```php
activity()
->causedBy(auth()->user()) // who
->performedOn($post) // what
->withProperties(['ip' => request()->ip()]) // extra data
->withProperties(['customProperty' => 'customValue'])
->log('updated post'); // description

$lastLoggedActivity = Activity::all()->last();

$lastLoggedActivity->subject; //returns an instance of an eloquent model
$lastLoggedActivity->causer; //returns an instance of your user model
$lastLoggedActivity->getProperty('customProperty'); //returns 'customValue'
$lastLoggedActivity->description; //returns 'Look mum, I logged something'

https://filamentphp.com/plugins/daniel-green-logger
https://filamentphp.com/plugins/alizharb-activity-log


```

```php
# Basic Retrieval via Controller

php artisan queue:work --max-time=3600

php artisan queue:work --sleep=3

php artisan queue:work --queue=high,low

php artisan queue:restart // restart queue after deployment

# Installing Supervisor
sudo apt-get install supervisor

# Configuring Supervisor

# Supervisor configuration files are typically stored in the /etc/supervisor/conf.d directory. Within this directory, you may create any number of configuration files
# create a laravel-worker.conf file

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/app.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/forge/app.com/worker.log
stopwaitsecs=3600

# You should ensure that the value of stopwaitsecs is greater than the number of seconds consumed by your longest running job.

# Starting Supervisor
sudo supervisorctl reread

sudo supervisorctl update

sudo supervisorctl start "laravel-worker:*"

```

## Idempotency

Idempotency for Laravel is a production-ready package for implementing idempotency in API requests. Clients can safely retry API calls using this package without worrying about duplicate processing.

Docs: https://github.com/infinitypaul/idempotency-laravel

```sh
POST /api/payments HTTP/1.1
Content-Type: application/json
Idempotency-Key: 123e4567-e89b-12d3-a456-426614174000

{
  "amount": 1000,
  "currency": "USD",
  "description": "Order #1234"
}

composer require infinitypaul/idempotency-laravel
php artisan vendor:publish --provider="Infinitypaul\Idempotency\IdempotencyServiceProvider"

```

## Cache Data

make sure you use Redis on production instead of file

```php
    $cacheKey = 'company_' . $this->company->id . '_business_metrics';
    // Cache for 60 minutes (3600 seconds)
    $businessMetric = Cache::remember($cacheKey, 3600, function () {
        return  $businessMetric = $this->company->businessMetrics()?->latest()?->first();
    });

    Cache::forget($cacheKey);

```

## Security

- Move JavaScript/Style out of your HTML
- Use random_bytes() instead of uniqid() or md5(time())
- Use laravel-csp for policy, Google Tag manager

## Hosting

- VPS
- Shared hosting doesn't provide node js, so you have to build locally and copy the build to your server.

    Package to help in deploying to hostinger
    Laravel package for automated deployment to Hostinger shared hosting with GitHub Actions support.
    Docs: https://github.com/thecodeholic/laravel-hostinger-deploy
    Video: https://youtu.be/3M-i6L5lQ4k?t=813

## 3 Tools to Draw/Manage Database Schema

1. DrawDB.app
2. DBDiagram.io (visualize any DBMS with DBML syntax)
3. RunSQL.com (table structure with any DBMS)

## Payment Subscription

Subscription only works on cards
Link (payment plan in paystack):

1. https://share.google/aimode/QBMxsFk0wPR3I9qXP
2. https://share.google/aimode/I9vjoK1PZoRpdUGu2

How to use Paystack's Charge Authorization API
Paystack's Subscription API and how it's powered by the Charge Authorization API
How to use the Subscription API
GitHub: https://github.com/PaystackOSS/sample-subscriptions-app
Video: https://youtube.com/watch?v=A1GoWOyB8oA&t=243s

user_id
model_type, model_id (polymorphic addresses)
email, phone country

a #ledger_based approach where every money movement is stored as a transaction record:
✅ CREDIT
✅ DEBIT
✅ PENDING
✅ SUCCESS
✅ FAILED

```php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Add columns to user table
         */
        Schema::table(config('paystacksubscription.user_table', 'users'), function (Blueprint $table) {
            $table->string('paystack_id')->nullable()->index();
            $table->string('paystack_authorization')->nullable();
            $table->string('paystack_email_token')->nullable();
        });


        /*
         * Create subscriptions table
         */
        Schema::create('dk_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger(app(config('paystacksubscription.model'))->getForeignKey());
            $table->string('name');
            $table->string('paystack_id')->unique();
            $table->string('paystack_status');
            $table->string('paystack_plan')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('email_token')->nullable();
            $table->string('authorization')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index([app(config('paystacksubscription.model'))->getForeignKey(), 'paystack_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('paystacksubscription.user_table', 'users'));
        Schema::dropIfExists(config('paystacksubscription.subscription_table', 'dk_subscriptions'));
    }
}

```
## Notification, Contact and messaging

Docs: https://share.google/aimode/59Rj7hVF1vKaFSDgc

## Filament grid bug fix

Docs: https://share.google/aimode/KsOFjP3siq82hzoek

## Filament Laravel Backup

Docs: https://filamentphp.com/plugins/shuvroroy-spatie-laravel-backup

```sh
    composer require shuvroroy/filament-spatie-laravel-backup
```

## Filament Health Check

Docs: https://filamentphp.com/plugins/shuvroroy-spatie-laravel-health

```sh
    composer require shuvroroy/filament-spatie-laravel-health
```

## Monitor Queue Jobs on all Driver

Docs: https://filamentphp.com/plugins/croustibat-jobs-monitor

```sh
    composer require croustibat/filament-jobs-monitor
    php artisan vendor:publish --tag="filament-jobs-monitor-migrations"
    php artisan migrate
```

## Using Cloudinary SDK wih Laravel

This SDK implements the File Storage Driver interface allowing you to use it as just another storage destination like s3, azure or local disk.

docs: https://laravel.cloudinary.dev/installation
link: https://cloudinary.com/blog/laravel-cloudinary-v2-release-update

```sh
composer require cloudinary-labs/cloudinary-laravel
php artisan cloudinary:install
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"

```

Add your Cloudinary credentials to your .env file:

```sh
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_UPLOAD_PRESET=your_upload_preset
CLOUDINARY_NOTIFICATION_URL=
```

using cloudinary with spatie media to upload in laravel

```php
    'disks' => [
        // ... other disks
        'cloudinary' => [
            'driver' => 'cloudinary',
            'key' => env('CLOUDINARY_API_KEY'), // Or extract from CLOUDINARY_URL
            'secret' => env('CLOUDINARY_API_SECRET'), // Or extract from CLOUDINARY_URL
            'cloud' => env('CLOUDINARY_CLOUD_NAME'), // Or extract from CLOUDINARY_URL
            'url' => env('CLOUDINARY_URL'),
            'secure' => (bool) env('CLOUDINARY_SECURE', true),
            'prefix' => env('CLOUDINARY_PREFIX'), // Optional: specify a folder prefix
        ],
    ],
```

## Media Library + Cloudinary

Docs: https://spatie.be/docs/laravel-medialibrary/v11/api/adding-files

```php
// Specify
$media = $model->addMediaFromRequest('file')
    ->usingMimeType('application/pdf') // or 'image/jpeg', 'text/plain', etc.
    ->toMediaCollection('documents');

// Auto detect
$file = $request->file('file');
$mimeType = $file->getClientmimeType(); // Or $file->getMimeType()

$model->addMedia($file)
      ->usingMimeType($mimeType)
      ->toMediaCollection('documents');


```

## Using test markdown

```php

    // Define a clean name for your target filesystem tracking asset
    $fileName = "converted_document_" . time() . ".md";
    $markdownContent = "hello world";

    // Save string layout data explicitly as a standard tracking node
    $mediaItem = $company->addMediaFromString($markdownContent)
        ->usingFileName($fileName)
        ->usingName('Converted Document Markdown')
        ->toMediaCollection('documents', 'public');
    $url = $mediaItem?->original_url;
    return $url;
```
## Filament Page

```php
    php artisan make:filament-resource Company --generate --soft-deletes
    Would you like to generate a read-only view page for the resource? ┐
 │ Yes (No view page or panel)
 │ No (With view page or panel)

```

### Filament Shield

```sh

php artisan shield:setup

```

Docs: https://share.google/aimode/LdPnspQNUNNy2sQmg

````php

// Pages
<?php

namespace App\Filament\Pages;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class MyPage extends Page
{
    use HasPageShield;
    ...
}

// Widgets
<?php

namespace App\Filament\Widgets;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class IncomeWidget extends LineChartWidget
{
    use HasWidgetShield;
    ...
}


// Using Select Component
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->searchable(),

// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
    ->relationship('roles', 'name')
    ->searchable(),

#Navigation

FilamentShieldPlugin::make()
    ->navigationLabel('Label')                  // string|Closure|null
    ->navigationIcon('heroicon-o-home')         // string|Closure|null
    ->activeNavigationIcon('heroicon-s-home')   // string|Closure|null
    ->navigationGroup('Group')                  // string|Closure|null
    ->navigationSort(10)                        // int|Closure|null
    ->navigationBadge('5')                      // string|Closure|null
    ->navigationBadgeColor('success')           // string|array|Closure|null
    ->navigationParentItem('parent.item')       // string|Closure|null
    ->registerNavigation();                     // bool|Closure

// run locally -
php artisan shield:generate --all --option=policies --panel=administrator

            # Filament Shield - Generate Missing Policies
            php artisan shield:generate --all --option=policies --panel=administrator

```sh

php artisan shield:generate --all --option=policies --panel=administrator

 Summary:

  # Policies generated .......................................................................................................................... 16
  # Permissions generated ........................................................................................................................ 0
  # Entities (Resources, Pages, Widgets) processed .............................................................................................. 34


php artisan shield:generate --all --option=permissions
php artisan shield:generate --all --option=widget

 Summary:

  # Policies generated ........................................................................................................................... 0
  # Permissions generated ...................................................................................................................... 194
  # Entities (Resources, Pages, Widgets) processed .............................................................................................. 34

````

- Protect some roles from being modified or delete

// config/filament-shield.php
// 'super_admin' => [
// 'enabled' => true,
// 'name' => 'super_admin',
// 'define_via_gate' => true,
// 'intercept_gate' => 'before',
// ],

```php

    // app/Policies/RolePolicy.phps
    use Spatie\Permission\Models\Role;
    use App\Models\User;

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Block modification of core roles by name
        if (in_array($role->name, ['super_admin', 'admin'])) {
            return false;
        }

        return $user->can('update_role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Block deletion of core roles by name
        if (in_array($role->name, ['super_admin', 'admin'])) {
            return false;
        }

        return $user->can('delete_role');
    }

```

// app/Filament/Resources/Shield/RoleResource.php.

```sh
    php artisan shield:publish
    php artisan shield:publish --panel=admin

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Instantly deny editing if the role matches your core roles
        if (in_array($record->name, ['super_admin', 'admin'])) {
            return false;
        }

        return parent::canEdit($record);
    }


```

## Flutterwave Docs:

Postman: https://www.postman.com/lively-zodiac-773579/flutterwave-api/folder/z88un5u/subscriptions?sideView=agentMode

## Business Profile

Using this guideline for a business "AMT Digital Networks LTD".
A technology, software and training company in Nigeria.
I need you to build a business profile:

    'rc_number',
    'cac_number',
    'tin_number',
    'business_name',
    'business_email',
    'website',
    'industry',
    'business_age',
    'team_size',
    'year_establish',
    'annual_revenue',
    'number_of_employees',

    // Data Sharing
    'allow_lender_view_score',
    'allow_investor_view_profile',
    'allow_partner_request_data',

    // state, city, address, country
    'address',
    'city',
    'state',
    'country',

Also provide all required documents, that the business needs.
You can use any available documents format from image, docs, and excel.
Make the documents looks as real as possible with the provided business profile.
I also provide a sample of the Nigeria TIN and CAC.
Thank you.

## Filament Media

```php

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('cover_url')
                ->label('Image Preview')
                ->circular(),

            TextColumn::make('document_url')
                ->label('View Document')
                ->url(fn ($record) => $record->document_url)
                ->openUrlInNewTab()
                ->icon('heroicon-o-arrow-top-right-on-square'),
        ]);
}



use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Placeholder::make('document_link')
                ->label('Uploaded File')
                ->content(function ($record) {
                    if (!$record || !$record->document_url) return '-';

                    return new HtmlString(
                        "<a href='{$record->document_url}' target='_blank' class='text-primary-600 hover:underline'>
                            View current document
                        </a>"
                    );
                }),
        ]);
}

// The Media resource
// Docs: https://share.google/aimode/ohCbHG8nntPIvboyb

use Filament\Tables;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('file_name')
                ->label('Preview')
                // This builds the public URL directly from the Spatie disk and path structure
                ->disk(fn ($record) => $record->disk)
                ->url(fn ($record) => $record->getUrl()),

            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('collection_name'),
        ]);
}


use Filament\Infolists;
use Filament\Infolists\Infolist;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\ImageEntry::make('file_name')
                ->label('Full Image')
                ->disk(fn ($record) => $record->disk)
                ->url(fn ($record) => $record->getUrl()),
        ]);
}
```

## Monitoring

```sh
    composer require laravel/nightwatch
    composer require sentry/sentry-laravel
```

## Laravel Mails

Collect everything about sent mails in your Laravel app

Docs: github.com/backstagephp/laravel-mails

Email as a protocol is very error prone.
Succesfull email delivery is not guaranteed in any way,
so it is best to monitor your email sending realtime

- The package also logs all events that are fired when an email is sent. This is useful to track the email sending process.

```sh
    composer require backstage/mails
    php artisan vendor:publish --tag="mails-migrations"
    php artisan migrate

    # Add the API key of your email service provider to the config/services.php file in your Laravel project, currently we only support Postmark and Mailgun:
    php artisan mail:webhooks [service] // where [service] is your provider, e.g. postmark or mailgun
    php artisan mail:webhooks smtp

    # .env
    MAILS_QUEUE_WEBHOOKS=true
    MAILS_LOGGING_ENABLED=true
    MAILS_ENCRYPTED=true
```

Note: [!IMPORTANT] For setting up the webhooks to register mail events, please look into the README of Laravel Mails, the underlying package that powers this package.

## Exporting data

Docs: https://filamentphp.com/docs/4.x/actions/export
Link: https://share.google/aimode/OeOXsn2Rzv1Eubqx7

```sh
FILESYSTEM_DISK=public
FILAMENT_FILESYSTEM_DISK=public

```

## Whatsapp Chat Message

chat_messages

- name
- email
- message
- ip
- created_at

## Filament user journey

Also create a view on the filament where Mr. Segun can track the user journey.

Just like a users table, with Plan,
Capital passport status[to check the capital passport level unlocked / generated],
Reports downloaded, etc.

Track Registration, Copmany, BHS, CRS, IRS, TRS.
To understand where users drop off and their next step.

php artisan make:filament-page TrackCompanyFlow --resource=CompanyResource

Docs: https://share.google/aimode/5Fwd9pPz0t0IQEOGI

php artisan make:filament-relation-manager CompanyResource capitalPassport capital_passport_no

┌ Do you want to link this to an existing resource? ───────────┐
│ No │
└──────────────────────────────────────────────────────────────┘

┌ Should there be a read-only "view" modal on the relation manager? ┐
│ No │
└───────────────────────────────────────────────────────────────────┘

┌ Should the configuration be generated from the current database columns? ┐
│ Yes │

php artisan make:filament-relation-manager CompanyResource businessHealthAnalysis company_id

──────────┐
│ No │
└──────────────────────────────────────────────────────────────┘

┌ Should there be a read-only "view" modal on the relation manager? ┐
│ No │
└───────────────────────────────────────────────────────────────────┘

┌ Should the configuration be generated from the current database columns? ┐
│ Yes │
└──────────────────────────────────────────────────────────────────────────┘

┌ What type of relationship is this? ──────────────────────────┐
│ Other │
└──────────────────────────────────────────────────────────────┘

## Filament action for resending re-verification mail

Docs: https://share.google/aimode/dlE26lzlZwftIEfma

## Add new filament widgets
## Filament Page

```php
    php artisan make:filament-resource Company --generate --soft-deletes
    Would you like to generate a read-only view page for the resource? ┐
 │ Yes (No view page or panel)
 │ No (With view page or panel)

```

### Filament Shield

```sh

php artisan shield:setup

```

Docs: https://share.google/aimode/LdPnspQNUNNy2sQmg

````php

// Pages
<?php

namespace App\Filament\Pages;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class MyPage extends Page
{
    use HasPageShield;
    ...
}

// Widgets
<?php

namespace App\Filament\Widgets;

use ...;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class IncomeWidget extends LineChartWidget
{
    use HasWidgetShield;
    ...
}


// Using Select Component
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->searchable(),

// Using CheckboxList Component
Forms\Components\CheckboxList::make('roles')
    ->relationship('roles', 'name')
    ->searchable(),

#Navigation

FilamentShieldPlugin::make()
    ->navigationLabel('Label')                  // string|Closure|null

Docs: https://www.youtube.com/watch?v=WdkzEDDgB7E
Docs: https://share.google/aimode/e2gIlt0igyIpzb6xJ
Using Trrends with custom filament schema form on stat

- How many users completed onboarding?
- How many users uploaded CAC TIN/Connected Bank?
- How many generated a Business Health Score?
- How many attempted to pay?
- How many created a Capital Passport?
- How many came back after their first visit?

## Tag a paln

payment page:

What do you want to achieve today?

○ Understand my business - show the 3 oneoffs bundles

○ Prepare for funding - capital passport bundle

○ Apply for investment - funding readiness bundle

○ Keep my Capital Passport current - subscription plan

```

```

## The Five UTM Parameters

UTM stands for Urchin Tracking Module. It refers to specific pieces of text added to the end of a web address (URL) to help analytics tools (like Google Analytics) track exactly where website visitors come from and which marketing efforts drive the most traffic.

- utm_source: Shows the specific origin of the traffic, such as google, facebook, or a newsletter.
- utm_medium: Explains the marketing channel type, such as cpc (cost-per-click), social, or email.
- utm_campaign: Names the exact project or sale event, such as spring-sale or product-launch.
- utm_term: Identifies specific paid search keywords (optional).
- utm_content: Differentiates specific links or ad layouts in the same campaign, like a text-link versus an image-banner (optional).

Link: https://veriscore.app/?utm_source=facebook&utm_medium=social&utm_campaign=product-launch-auguest-2026

## BVN + Credit history

Overview:

what are the steps I need to verify a business BVN on my application and user Mono to pull their business account credit history, score it analyze (codebase analysis + LLM summary) it and create a report about it.

Verify it with current company information, group it by institutions, providers (checking both CRC and XDS to ensure no past debts, loan defaults, or repayment records are missed), credit performance, date, loan status and more...

- bvn (This required a dedicated setup, beacause the user need to enter an OTP before the information can be retrieve, they will also be pre verification, and post verification) - [similar setup like the CAC/TIN]
  https://docs.mono.co/docs/lookup/bvn-igree

- credit history
  Docs: https://docs.mono.co/docs/lookup/credit-history-lookup
  Docs API endpoint: https://docs.mono.co/api/lookup/credit-history

The credit history should be unique in the database, it can be sort, filter, select by group etc.


----

Act as a Principal Laravel Engineer. Write a clean, production-ready implementation for verifying a business BVN using Mono iGree, fetching dual-bureau credit history (CRC & XDS), and generating a dynamic credit report.

### Architecture & Constraints:
- Framework: Laravel (Latest version)
- Business Logic: Must be isolated inside a dedicated Service layer (`app/Services/MonoService.php`).
- Controller: `app/Http/Controllers/V1/Company/MonoController.php` (Refactored to match standard naming conventions).
- Routes: API routes under `api/v1/company/*`.
- Data Context: We can access company owner information using `$company?->owners`.
- API Integrations: Use Laravel's HTTP Client (`Http::`) to interact with Mono API endpoints (`v2/lookup/bvn/initiate`, `v2/lookup/bvn/verify`, and `v3/lookup/credit-history/all`).

### Task Requirements:

1. DB Schema & Migration (`create_company_credit_histories_table`):
   - Fields: id, company_id, owner_bvn, provider (CRC/XDS), institution_name, account_number, loan_status (performing, non-performing, default, closed), currency, amount_disbursed, current_balance, amount_overdue, disbursement_date, repayment_history (json), raw_response (json), timestamps.
   - Database Constraint: Create a composite unique index on `['company_id', 'provider', 'account_number', 'disbursement_date']` to ensure unique entries and prevent duplication.

2. Mono Service Layer (`app/Services/MonoService.php`):
   - Method `initiateBvnLookup($bvn)`: Triggers Mono iGree v2 (`/v2/lookup/bvn/initiate`) with identity scope. Returns session ID and channels.
   - Method `verifyBvnOtp($sessionId, $channel, $otp)`: Inputs OTP to fetch final BVN identity verification.
   - Method `fetchAndStoreCreditHistory($companyId, $bvn)`: Calls Mono v3 dual-bureau credit endpoint (`/v3/lookup/credit-history/all`), processes raw data from both CRC and XDS, and safely upserts unique entries into the database.
   - Method `generateCreditReport($companyId)`: Queries the database, groups data dynamically (by Institution and by Provider), runs a scoring calculation algorithm (out of 850 points based on overdue amounts and non-performing statuses), and returns an analytical payload structure.

3. Controller Layer (`app/Http/Controllers/V1/Company/MonoController.php`):
   - Methods needed: `initiateBvn(Request $request)`, `verifyBvnAndFetchCredit(Request $request)`, and `getCreditReport($companyId)`.
   - Leverage `$request->user()->company?->owners` context where applicable to validate ownership before processing requests.
   - Implement structured JSON responses with appropriate HTTP success/error status codes.

4. API Routes (`routes/api.php`):
   - Provide the exact `Route::prefix('v1/company')->group(...)` block pointing to the controller methods.

Please write clean, well-typed code, using Form Requests for validation, strict type-hinting, and standard Laravel HTTP exception handling. Do not include pseudocode.

