<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\Tec;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormFilterRequest extends FormRequest
{
    public function rules(): array
    {
        $tec_ids = Tec::all()->pluck('id')->toArray();
        array_unshift($tec_ids, 0);
        $client_ids = Client::all()->pluck('id')->toArray();
        array_unshift($client_ids, 0);

        return [
            'client_id' => ['nullable', 'numeric',Rule::in($client_ids)],
            'finished' => 'required|numeric|min:0|max:2',
            'tec_id' => ['nullable', 'numeric',Rule::in($tec_ids)],
            'date_type' => ['required', Rule::in(['order_open_date', 'last_note_date'])],
            'date_start' => 'required|date_format:Y-m-d',
            'date_end' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.numeric' => 'Selecione um cliente válido',
            'client_id.in' => 'Selecione um cliente cadastrado',
            'finished.required' => 'Selecione um status de SAT',
            'finished.numeric' => 'Selecione um status de SAT válido',
            'finished.min' => 'Código de status da SAT tem que ser maior que zero',
            'finished.max' => 'Código de status da SAT tem que ser menor do que 3',
            'tec_id.numeric' => 'Selecione um técnico válido',
            'tec_id.in' => 'Selecione um técnico cadastrado',
            'date_type.required' => 'Selecione um tipo de data para filtrar',
            'date_type.in' => 'Selecione um tipo de data válido',
            'date_start.required' => 'Selecione uma data inicial',
            'date_start.date_format' => 'Data inicial inválida',
            'date_end.required' => 'Selecione uma data final',
            'date_end.date_format' => 'Data final inválida',
        ];
    }
}
