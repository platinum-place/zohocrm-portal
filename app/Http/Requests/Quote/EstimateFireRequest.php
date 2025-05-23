<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class EstimateFireRequest extends FormRequest
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
            'idGiroDelNegocio' => ['required', 'integer'],
            'MontoOriginal' => ['required', 'numeric'],
            'idTipoEmpleado' => ['required', 'integer'],
            'FormaDePago' => ['required', 'string', 'in:Mensual,PagoTotal'],
            'FechaEmision' => ['required', 'date_format:d/m/Y'],
            'FechaVencimiento' => ['required', 'date_format:d/m/Y'],
            'IdentCliente' => ['required', 'string'],
            'Cliente' => ['required', 'string'],
            'Telefono' => ['required', 'string'],
            'ValorFinanciado' => ['required', 'numeric'],
            'Construccion' => ['required', 'boolean'],
            'TipoContruccion' => ['required', 'string', 'in:Superior,SinConstruccion'],
            'Ubicación' => ['required', 'string'],
            'Error' => ['nullable', 'string'],
            'Codeudor' => ['nullable', 'boolean'],
            'Vida' => ['nullable', 'boolean'],
            'EdadCodeudor' => ['nullable', 'integer'],
            'FinanciarSeguro' => ['nullable', 'boolean'],
        ];
    }
}
