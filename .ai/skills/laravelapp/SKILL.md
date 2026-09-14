---
name: standard-laravel
description: Guidelines and standards for constructing versioned REST APIs using Passport/Sanctum, Spatie Data, Filament dashboard, Filament CORS, and clean service layers.
---

# Idea App: Core Architectural Standards

Always adhere strictly to these architectural boundaries, folder separations, and dependency paradigms when generating or maintaining code.

---

## 1. Directory Structure & Case Conventions

To align with uniform web standards and strict PSR-4 class auto-loading, enforce this explicit case distribution:

*   **API Routing Layers:** Must reside in lowercase subfolders:
    *   `routes/v1/web.php` (Public React/Vue/Mobile endpoint map)
    *   `routes/v1/admin.php` (Admin programmatic entry channels)
    *   `routes/v1/user.php` (User programmatic entry channels)
*   **API Controller Definitions:** Must reside in capitalized, PascalCase namespaces:
    *   `app/Http/Controllers/Api/V1/`
*   **Data Object Structures (Spatie Data):** Must reside in capitalized, PascalCase namespaces:
    *   `app/Data/V1/`
*   **Identity Models:** Must utilize **UUID primary keys** via the native `HasUuids` model trait. Never expose auto-incrementing integer sequences.

---

## 2. Dynamic CORS Control

Never hardcode consumer domain strings inside `config/cors.php`. The application dynamically queries allowed cross-domain endpoints using a cached database layer managed via Filament (unless when stated):

```php
// config/cors.php snippet
$fallbackOrigins = ['http://localhost:3000', 'http://localhost:5173'];

if (app()->runningInConsole() || !Schema::hasTable('allowed_origins')) {
    $allowedOrigins = $fallbackOrigins;
} else {
    $allowedOrigins = Cache::remember('cors_allowed_origins', 600, function () use ($fallbackOrigins) {
        $dbOrigins = \App\Models\AllowedOrigin::where('is_active', true)->pluck('domain')->toArray();
        return array_merge($fallbackOrigins, $dbOrigins);
    });
}
```
*Note: Ensure all dashboard inputs validating consumer origins strictly forbid trailing slashes (`/`) and demand explicit communication protocols (`https://`).*

---

## 3. The Clean Data Flow Layer (DDD Lite)

Controllers must remain slim delivery components. Business operations must be cleanly decoupled using the following pattern:

1.  **Incoming Validation:** Handled by a typed `Spatie\LaravelData\Data` transfer object matching the active version directory. Never inject traditional `Request` or `FormRequest` classes directly into core logic.
2.  **Query & Filtering:** Handled by a dedicated Service class using `spatie/laravel-query-builder` to safely allow filtering, partial searches, explicit sorts, and relation sideloading.
3.  **Core Business Logic:** Handled by dedicated domain Service classes (`app/Services/`).
4.  **Outgoing Serialization:** Models are transformed directly back into typed `Data` structures to secure internal database layouts from structural entity leaks.

## 4. Versioned Service-Layer Pattern (QueryBuilder & Spatie Data)

Always isolate business logic, Eloquent queries, and data transformations into versioned Service classes (`app/Services/V{X}/`). Controllers must remain completely thin, acting only as the entry and exit boundary.

### A. Directory & Namespacing Execution

- Services must live in: `app/Services/V1/` (PascalCase namespace matching route versions).
- Service methods that fetch listings must run Spatie QueryBuilder, handle pagination, and transform data using `PostData::collect()` before returning it to the controller.

### B. Service Class Reference Architecture (`app/Services/V1/PostService.php`)

```php
namespace App\Services\V1;

use App\Models\Post;
use App\Data\V1\PostData;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function getPaginatedPosts(int \$perPage = 15): mixed
    {
        // Execute Spatie QueryBuilder configurations
        \$query = QueryBuilder::for(Post::class)
            ->allowedIncludes(['user', 'media'])
            ->allowedFilters([
                'status',
                AllowedFilter::partial('title'),
            ])
            ->allowedSorts(['created_at', 'title'])
            ->defaultSort('-created_at');

        \$paginatedResults = query->paginate(perPage)->withQueryString();

        // Always transform into a Spatie Data collection before returning
        return PostData::collect(\$paginatedResults);
    }
}
```

### C. Thin Controller Reference Architecture (`app/Http/Controllers/Api/V1/PostController.php`)

Controllers must inject the Service class natively via dependency injection and return the service payload with zero inline query or formatting boilerplate:
```php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\V1\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        protected PostService \$postService
    ) {}

    public function index(Request \$request)
    {
        \$perPage = \$request->query('per_page', 15);
        
        // Directly return the pre-compiled Spatie Data collection from the service
        return \$this->postService->getPaginatedPosts(\$perPage);
    }
}
```

### Implementation Reference:

```php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Data\V1\PostData;
use App\Services\PostQueryService;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(
        protected PostQueryService $queryService,
        protected PostService $postService
    ) {}

    public function index()
    {
        return PostData::collect($this->queryService->getPaginatedBlogPosts());
    }

    public function store(PostData $data): PostData
    {
        $post = $this->postService->storePost($data, auth()->id());
        return PostData::from($post);
    }
}
```

---

## 4. Stateless Authentication Tracking

The application uses **Laravel Passport** or **Laravel Sanctum** for stateless token authentication. Standard session authentication tracking does not capture REST traffic out of the box. You must manually trigger identity audits when generating authentication handshakes:

```php
use Rappasoft\LaravelAuthenticationLog\Events\Login;
use Rappasoft\LaravelAuthenticationLog\Events\FailedLogin;

// Within API Login & Socialite Authentication callback routines:
event(new Login('api', $user, request())); // On Success
event(new FailedLogin('api', $email, $user)); // On Failure
```

---

## 5. Active Administrative Ecosystem Packages

When extending features inside the unified management workspace, leverage the designated security ecosystem plugins within `AdminPanelProvider.php`:
*   `Bezhanalleh\FilamentShield\FilamentShieldPlugin` ➔ Handles visually driven permission matrices.
*   `DanielGreen\Logger\FilamentLoggerPlugin` ➔ Provides model mutation histories.
*   `Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin` ➔ Tracks session states and suspicious travel anomalies.
*   `JohnRivera7\FilamentAntivirus\FilamentAntivirusPlugin` ➔ Intercepts `Spatie\MediaLibrary` uploads to enforce malware scanning via ClamAV.
*   `JohnRivera7\FilamentCybersecurity\FilamentCybersecurityPlugin` ➔ Monitors continuous security postures.
*   `PavelDenisov\FilamentLogViewer\FilamentLogViewerPlugin` ➔ Renders active server traces.

---

## 6. Performance & Scale Guidelines

*   **Queues:** Run standard database background queues (`QUEUE_CONNECTION=database`) for file transformations, social webhooks, and asynchronous workflows. (Optimize using persistent platforms like Horizon or Octane only in later scale stages).
*   **Documentation:** Ensure all `Spatie\LaravelData` shapes expose clear properties. **Dedoc Scramble** natively evaluates these variables to generate dynamic Swagger specs at `/docs/api` automatically.

## 6. User-Centric Profiles & Attribute Accessors

When designing or extending the core identity layers, prioritize strict data encapsulation, dynamic profile string combination, and typed attribute accessors. Never leak raw passwords or direct, unmapped column properties to public JSON clients.

### A. Core Model Configuration & Name Accessor (`app/Models/User.php`)

User identities must implement UUID keys natively, use modern Laravel `Attribute` mapping closures, and stack access logs seamlessly:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Passport\HasApiTokens; // or using default sanctum
use Spatie\Permission\Traits\HasRoles;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLogable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasUuids, HasRoles, AuthenticationLogable, InteractsWithMedia;

    protected \$keyType = 'string';
    public \$incrementing = false;

    protected \$fillable = [
        'email', 'password', 'first_name', 'last_name', 'middle_name', 'phone', 'avatar', 'is_active'
    ];

    /**
     * Dynamically combine name metrics into a clean, unified space-separated string.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                \$parts = array_filter([\$this->first_name, \$this->middle_name, \$this->last_name]);
                return !empty(\$parts) ? implode(' ', \$parts) : '';
            }
        );
    }
}
```

### B. Strongly-Typed Data Mapping Contract (`app/Data/V1/UserData.php`)

Always transform models into structural DTO envelopes. Ensure database columns are safely converted into clean JSON keys (e.g. camelCase or snake_case) expected by frontends:
```php
namespace App\Data\V1;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class UserData extends Data
{
    public function __construct(
        public string \$id,
        public string \$name, // Dynamically sourced from the Model accessor
        public ?string \$firstName,
        public ?string \$lastName,
        public ?string \$middleName,
        public string \$email,
        public ?string \$phone,
        public bool \$isActive,

        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d\TH:i:s\Z')]
        public string \$createdAt,
    ) {}

    public static function fromModel(\App\Models\User \$user): self
    {
        return new self(
            id: \$user->id,
            name: \$user->name,
            firstName: \$user->first_name,
            lastName: \$user->last_name,
            middleName: \$user->middle_name,
            email: \$user->email,
            phone: \$user->phone,
            isActive: (bool) \$user->is_active,
            createdAt: \$user->created_at,
        );
    }
}
```

### C. Forced API JSON Envelope Structure (Success, Errors, and Exceptions)

All JSON responses, whether returned from a standard query or caught globally via `app/Exceptions/ApiExceptionRenderer.php`, must conform to this exact top-level envelope schema:

```json
{
  "success": false,
  "message": "Validation failed / Resource not found / Unauthenticated",
  "data": null,
  "errors": [
    "Target error description string or multi-field error matrix array"
  ],
  "code": 422,
  "metadata": {
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 42
    },
    "links": {
      "first": "https://saas.com",
      "last": "https://saas.com"
    }
  }
}
```

#### Key Formatting Constraints:

1. `success`: **Boolean** flag matching the operation outcome.
2. `message`: **String** containing the primary operation summary or localized response message text.
3. `data`: **Array/Object or Null** housing the core payload if available.
4. `errors`: **Array or Null** containing multi-field error maps or trace text strings during an exception.
5. `code`: **Integer** matching the exact HTTP status code.
6. `metadata`: **Object or Null** containing nested `pagination` records and `links` objects generated from collections.

### D. Always add comment and Log information where necessary 

- Log::info('message', [])
- [array]
- single line comment // or multi line comment