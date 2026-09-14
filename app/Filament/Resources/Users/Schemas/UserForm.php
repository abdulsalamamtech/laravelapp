<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\System\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('two_factor_auth')
                    ->required()->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()->disabled(),
                DateTimePicker::make('email_verified_at')->format('m/d/Y,hms'),
                // TextInput::make('password')
                //     ->password()
                //     ->required(),
                CheckboxList::make('roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        $superAdminRole = Role::where('name', 'super_admin')->first();
                        if ($record->email === 'admin@gmail.com' && $superAdminRole && ! in_array($superAdminRole->id, $state)) {
                            $state[] = $superAdminRole->id;
                        }

                        // no team setup
                        $record->roles()->sync($state);
                        // $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
                    })
                    ->searchable(),
            ]);
    }
}
