<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->rol === 'admin';
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos', 'nombre')->ignore($this->route('producto')),
            ],
            'descripcion'  => 'required|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio'       => 'required|numeric|gt:0',
            'existencias'  => 'required|integer|min:0',
            'estado'       => [
                'required',
                'in:Disponible,Agotado',
                function ($attribute, $value, $fail) {
                    if ($this->existencias > 0 && $value === 'Agotado') {
                        $fail('No puede estar Agotado si hay existencias.');
                    }
                    if ($this->existencias == 0 && $value === 'Disponible') {
                        $fail('No puede estar Disponible si las existencias son 0.');
                    }
                },
            ],
        ];
    }
}