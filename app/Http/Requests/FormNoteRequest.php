<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'equip_mod' => 'required|max:20',
            'equip_id' => 'required|max:20',
            'equip_type' => 'required|max:20',
            'situation' => 'required|max:70',
            'cause' => 'required|max:80',
            'services' => 'required|max:330',
            'date' => 'required|date_format:Y-m-d',
            'go_start' => 'required|date_format:H:i',
            'go_end' => 'required|date_format:H:i',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'back_start' => 'required|date_format:H:i',
            'back_end' => 'required|date_format:H:i',
            'km_start' => 'required|numeric|min:0|max:9999.99',
            'km_end' => 'required|numeric|min:0|max:9999.99',
            'food' => 'required|numeric|min:0|max:9999.99',
            'expense' => 'numeric|min:0|max:9999.99',
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
            //
        ];
    }
}
