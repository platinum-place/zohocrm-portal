<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class InspectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cotz_id' => ['required', 'integer'],
            'passcode' => ['required', 'string', 'size:4'],
            'Correo' => ['required', 'email'],
            'CantPasajeros' => ['required', 'integer'],
            'Cilindros' => ['required', 'integer'],
            'Odómetro' => ['required', 'integer'],
            'unidadOdometro' => ['required', 'string'],
            'Foto1' => ['required', 'string','max:10000000'],
            'Foto2' => ['required', 'string'],
            'Foto3' => ['required', 'string'],
            'Foto4' => ['required', 'string'],
            'Foto5' => ['required', 'string'],
            'Foto6' => ['required', 'string'],
            'Foto7' => ['required', 'string'],
            'Foto8' => ['required', 'string'],
            'Foto9' => ['required', 'string'],
            'Foto13' => ['required', 'string'],
            'Foto10' => ['nullable', 'string'],
            'Foto11' => ['nullable', 'string'],
            'Foto12' => ['nullable', 'string'],
            'Foto14' => ['nullable', 'string'],
        ];
    }
}
