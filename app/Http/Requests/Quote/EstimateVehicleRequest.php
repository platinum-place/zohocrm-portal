<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class EstimateVehicleRequest extends FormRequest
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
            'NombreCliente' => ['required', 'string', 'max:255'],
            'FechaNacimiento' => ['required', 'date'],
            'IdCliente' => ['required', 'string', 'max:20'],
            'TelefResidencia' => ['required', 'string'],
            'TelefMovil' => ['required', 'string'],
            'TelefTrabajo' => ['required', 'string'],
            'Marca' => ['required', 'integer'],
            'Modelo' => ['required', 'integer'],
            'Anio' => ['required', 'digits:4'],
            'Chasis' => ['required', 'string', 'max:50'],
            'TipoVehiculo' => ['required', 'integer'],
            'MontoAsegurado' => ['required', 'integer', 'min:1'],
            'UsosGarantiasId' => ['required', 'array'],
            'UsosGarantiasId.*' => ['integer'],
            'Email' => ['required', 'email'],
            'Accesorios' => ['nullable', 'array'],
            'Accesorios.*' => ['string'],
            'Actividad' => ['nullable', 'string'],
            'Placa' => ['nullable', 'string', 'max:20'],
            'CirculacionID' => ['nullable', 'array'],
            'CirculacionID.*' => ['integer'],
            'ColorId' => ['nullable', 'array'],
            'ColorId.*' => ['integer'],
        ];
    }
}
