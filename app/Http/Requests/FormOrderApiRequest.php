<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormOrderApiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_type_id' => ['required','numeric',Rule::exists('order_types', 'id')],
            'sector' => 'required|max:30',
            'req_descr' => 'required|max:470',
            'equipment' => 'max:70',
        ];
    }


    public function messages()
    {
        return [
            'order_type_id.required' => 'Selecione um tipo de Serviço',
            'order_type_id.numeric' => 'Selecione um tipo de Serviço',
            'order_type_id.in' => 'Selecione um tipo de Serviço válido',
            'sector.required' => 'Digite o setor',
            'sector.max' => 'Setor deve ter no máximo 30 caracteres',
            'req_descr.required' => 'Insira a descrição do problema',
            'req_descr.max' => 'Problema deve ter no máximo 470 caracteres',
            'equipment.max' => 'Equipamento deve ter no máximo 70 caracteres',
        ];
    }
}
