<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Account;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterAccount extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Account';
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

    protected function handleRegistration(array $data): Account
    {
        $account = Account::create($data);

        $account->users()->attach(auth()->user());

        return $account;
    }
}
