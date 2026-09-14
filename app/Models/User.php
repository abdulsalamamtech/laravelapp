<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'phone_number',
    'password',
    // '2fa',
    'app_role',
    'two_factor_auth',
    'two_factor_type',
    'send_unverified_mail_at',

    'first_name',
    'last_name',
    'middle_name',
    'avatar',
    'google_id',
    'github_id',
    'is_active',
    'address',
    'city',
    'state',
    'country',
    'currency',
])]
#[Hidden([
    'password',
    'remember_token',
])]
#[WithoutIncrementing]
class User extends Authenticatable implements FilamentUser, HasMedia
{
    use AuthenticationLoggable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;

    /** @use HasFactory<UserFactory> */
    use HasUuids;

    use InteractsWithMedia;
    use LogsActivity;
    use Notifiable;

    // uuid as primary key
    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            // For many-to-many (belongsToMany) relationships, use detach() instead of delete()
            // $user->roles()->detach();
        });
    }

    /**
     * Laravel Filament integration - determine if user can access Filament admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // cache the user detail and log once in 30 minutes to avoid log flooding and still return true for access
        if (cache()->missing('user_'.$this->id)) {
            Log::info('User '.$this->id.' accessed Filament admin panel');
            cache()->put('user_'.$this->id, true, now()->addMinutes(30));
            Log::info('canAccessPanel called for user: '.$this->email.' for panel: '.$panel->getId());
        }

        return true;
    }

    /**
     * Check if this model instance is the currently logged-in user.
     */
    public function isCurrentAuthenticatedUserFromFilament(): bool
    {
        // Use Filament's auth helper to check against the active panel guard
        return Filament::auth()->check() && Filament::auth()->id() === $this->id;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('user profile')
            ->setDescriptionForEvent(function (string $eventName): string {
                if ($eventName === 'created') {
                    return 'user profile created';
                }

                if ($eventName === 'updated') {
                    return 'user information updated';
                }

                if ($eventName === 'deleted') {
                    return 'user profile deleted';
                }

                return 'user information updated';
            });
    }

    // Relationship
    // public function team()
    // {
    //     return $this->belongsTo(Team::class);
    // }
    public function notifyAuthenticationLogVia(): array
    {
        // return ['vonage', 'mail', 'slack'];
        return ['mail'];
    }

    /**
     * Get the company this user owns.
     *
     * NOTE: The Company domain (App\Models\Company, CompanyEmployee) is currently
     * not present in the codebase. This method is deactivated until it is restored.
     */
    public function userCompany(): null
    {
        Log::info('User model - Company domain is not yet available, userCompany() returned null', [
            'user_id' => $this->id,
        ]);

        return null;
    }

    /**
     * Check if the user's company has a boolean privilege enabled.
     *
     * NOTE: Deactivated because the Company/privilege domain is not present.
     */
    public function hasCompanyPrivilege(string $slug): bool
    {
        return false;
    }
}
