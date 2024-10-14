<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'sector' => 'required|max:30',
            'req_name' => 'required|max:20',
            'req_date' => 'required|date_format:Y-m-d',
            'req_time' => 'required|date_format:H:i',
            'req_descr' => 'required|max:70',
            'equipment' => 'max:70',
        ];
    }
}
