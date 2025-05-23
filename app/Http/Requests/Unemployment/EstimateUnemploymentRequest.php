<?php

namespace App\Http\Requests\Unemployment;

use Illuminate\Foundation\Http\FormRequest;

class EstimateUnemploymentRequest extends FormRequest
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
            'Cuota' => ['required', 'numeric'],
            'Plazo' => ['required', 'integer'],
            'TiempoLaborando' => ['required', 'integer'],
            'MontoOriginal' => ['required', 'numeric'],
            'idTipoEmpleado' => ['required', 'integer'],
            'FormaDePago' => ['required', 'string', 'in:Mensual,PagoTotal'],
            'IdentCliente' => ['required', 'string'],
            'Cliente' => ['required', 'string'],
            'Direccion' => ['required', 'string'],
            'Telefono' => ['required', 'string'],
            'FinanciarSeguro' => ['nullable', 'boolean'],
        ];
    }
}
