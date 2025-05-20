<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class EstimateLifeRequest extends FormRequest
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
            'FechaEmision' => ['required', 'date'],
            'FechaVencimiento' => ['required', 'date'],
            'Edad' => ['required', 'integer'],
            'PlazoAnios' => ['required', 'integer'],
            'PlazoDias' => ['required', 'integer'],
            'MontoOriginal' => ['required', 'numeric'],
            'NombreCliente' => ['required', 'string', 'max:255'],
            'IdenCliente' => ['required', 'string', 'max:50'],
            'FechaNacimiento' => ['required', 'date'],
            'Telefono1' => ['required', 'string'],
            'Direccion' => ['required', 'string', 'max:255'],
            'codeudor' => ['nullable', 'boolean'],
            'EdadCodeudor' => ['nullable', 'integer'],
        ];
    }
}
