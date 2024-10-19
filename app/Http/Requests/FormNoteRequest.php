<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'equip_mod' => 'required|max:20',
            'equip_id' => 'required|max:20',
            'equip_type' => 'required|max:20',
            'note_type_id' => ['required','numeric','min:1',Rule::in(session('n_types_ids'))],
            'defect_id' => ['required','numeric','min:1',Rule::in(session('defects_ids'))],
            'cause_id' => ['required','numeric','min:1',Rule::in(session('causes_ids'))],
            'solution_id' => ['required','numeric','min:1',Rule::in(session('solutions_ids'))],
            'services' => 'max:270',
            'date' => 'required|date_format:Y-m-d',
            'go_start' => 'required|date_format:H:i',
            'go_end' => 'required|date_format:H:i',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'back_start' => 'required|date_format:H:i',
            'back_end' => 'required|date_format:H:i',
            'km_start' => 'min:0|max:9999.99',
            'km_end' => 'min:0|max:9999.99',
            'food' => 'min:0|max:9999.99',
            'expense' => 'min:0|max:9999.99',
            'obs' => 'max:40',
            'first_tec' => 'required',
            'sign_t_1' => 'required',
            'cl_name' => 'max:40',
            'cl_function' => 'max:40',
            'cl_contact' => 'max:40',
            'finished' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'equip_mod.required' => 'Digite o modelo do equipamento',
            'equip_mod.max' => 'Modelo do equipamento não deve ter mais de 20 caracteres',
            'equip_id.max' => 'ID do equipamento não deve ter mais de 20 caracteres',
            'equip_id.required' => 'Digite o ID do equipamento',
            'equip_type.required' => 'Digite o tipo do equipamento',
            'equip_type.max' => 'Tipo do equipamento não deve ter mais de 20 caracteres',
            'note_type_id.required' => 'Selecione um tipo de serviço',
            'note_type_id.numeric' => 'Selecione um tipo de serviço válido',
            'note_type_id.min' => 'Selecione um tipo de serviço válido',
            'note_type_id.in' => 'Selecione um tipo de serviço válido',
            'defect_id.required' => 'Selecione o defeito',
            'defect_id.numeric' => 'Selecione o defeito',
            'defect_id.min' => 'Selecione o defeito',
            'defect_id.in' => 'Selecione um defeito válido',
            'cause_id.required' => 'Selecione uma possível causa',
            'cause_id.numeric' => 'Selecione uma possível causa',
            'cause_id.min' => 'Selecione uma possível causa',
            'cause_id.in' => 'Selecione uma possível causa válida',
            'solution_id.required' => 'Selecione a solução executada',
            'solution_id.numeric' => 'Selecione a solução executada',
            'solution_id.min' => 'Selecione a solução executada',
            'solution_id.in' => 'Selecione uma solução válida',
            'services.max' => 'Serviços não podem ter mais de 270 caracteres',
            'date.required' => 'Selecione a data',
            'date.date_format' => 'Data inválida',
            'go_start.required' => 'Selecione o horário de saída (Ida)',
            'go_start.date_format' => 'Horário de saída inválido (Ida)',
            'go_end.required' => 'Selecione o horário de chegada (Ida)',
            'go_end.date_format' => 'Horário de chegada inválido (Ida)',
            'start.required' => 'Selecione o horário de Início',
            'start.date_format' => 'Horário de início inválido',
            'end.required' => 'Selecione o horário de término',
            'end.date_format' => 'Horário de término inválido',
            'back_start.required' => 'Selecione o horário de saída (Retorno)',
            'back_start.date_format' => 'Horário de saída inválido (Retorno)',
            'back_end.required' => 'Selecione o horário de chegada (Retorno)',
            'back_end.date_format' => 'Horário de chegada inválido (Retorno)',
        ];
    }
}
