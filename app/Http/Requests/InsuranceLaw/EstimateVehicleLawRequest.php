<?php

namespace App\Http\Requests\InsuranceLaw;

use Illuminate\Foundation\Http\FormRequest;

class EstimateVehicleLawRequest extends FormRequest
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
            'Documento' => ['required', 'string', 'max:15'],
            'NombreCliente' => ['required', 'string', 'max:100'],
            'DireccionC' => ['nullable', 'string', 'max:150'],
            'TelefonoC' => ['required', 'string', 'max:15'],
            'CorreoC' => ['nullable', 'string', 'email', 'max:25'],
            'IDTipoVehiculo' => ['required', 'integer'],
            'Marca' => ['required', 'string', 'max:30'],
            'Modelo' => ['required', 'string', 'max:25'],
            'Anio' => ['required', 'integer'],
            'Chassis' => ['required', 'string', 'min:6', 'max:17'],
            'Placa' => ['nullable', 'string', 'max:7'],
            'Poliza' => ['nullable', 'string', 'max:15'],
            'Prima' => ['required', 'numeric'],
            'Cobertura' => ['required', 'string', 'max:12'],
            'FianzaJudicial' => ['required', 'numeric'],
            'Usuario' => ['required', 'string', 'max:15'],
            'PDV' => ['required', 'string', 'max:20'],
        ];
    }
}
