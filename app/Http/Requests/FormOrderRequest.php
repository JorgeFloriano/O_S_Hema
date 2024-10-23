<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => 'required|numeric|min:1',
            'order_type_id' => ['required','numeric','min:1',Rule::in(session('types_ids'))],
            'sector' => 'required|max:30',
            'req_name' => 'required|max:20',
            'req_date' => 'required|date_format:Y-m-d',
            'req_time' => 'required|date_format:H:i',
            'req_descr' => 'required|max:430',
            'equipment' => 'max:70',
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'Selecione um Cliente válido',
            'client_id.min' => 'Selecione um Cliente válido',
            'client_id.numeric' => 'Selecione um Cliente válido',
            'order_type_id.required' => 'Selecione um tipo de Serviço',
            'order_type_id.min' => 'Selecione um tipo de Serviço',
            'order_type_id.numeric' => 'Selecione um tipo de Serviço',
            'order_type_id.in' => 'Selecione um tipo de Serviço válido',
            'sector.required' => 'Insira o setor',
            'sector.max' => 'Setor deve ter no máximo 30 caracteres',
            'req_name.required' => 'Insira o nome do solicitante',
            'req_name.max' => 'Nome do solicitante deve ter no máximo 20 caracteres',
            'req_date.required' => 'Insira a data',
            'req_date.date_format' => 'Data inválida',
            'req_time.required' => 'Insira a hora',
            'req_time.date_format' => 'Hora inválida',
            'req_descr.required' => 'Insira a descrição do problema',
            'req_descr.max' => 'Problema deve ter no máximo 70 caracteres',
            'equipment.max' => 'Equipamento deve ter no máximo 70 caracteres',
        ];
    }
}
