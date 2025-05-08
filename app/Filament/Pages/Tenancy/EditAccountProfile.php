<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditAccountProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Account profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('identifier')
                    ->required()
                    ->unique(),
            ]);
    }
}
