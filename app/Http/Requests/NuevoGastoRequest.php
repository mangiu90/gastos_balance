<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NuevoGastoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'tipo' => ['required'],
            'monto' => ['required', 'numeric', 'gt:0', 'max:9999999999999999'],
            'detalle' => ['nullable', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'tipo' => 'Tipo',
            'monto' => 'Monto',
            'detalle' => 'Detalle',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
