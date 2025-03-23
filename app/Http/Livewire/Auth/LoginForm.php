<?php

namespace App\Http\Livewire\Auth;

use App\Clients\ZohoClient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Livewire\Component;

class LoginForm extends Component
{
    public $username;
    public $password;

    protected $rules = [
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ];

    public function messages(): array
    {
        return [
            'username.required' => __('validation.required', ['attribute' => __('Username')]),
            'password.required' => __('validation.required', ['attribute' => __('Password')]),
        ];
    }

    public function submit()
    {
        $this->validate();

        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                if (!Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
                    $validator->errors()->add('username', __('auth.failed'));
                }
            });
        })->validate();

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
