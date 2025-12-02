<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FormApiNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authentication
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Required fields
            'order_id' => 'required|exists:orders,id',
            'equip_mod' => 'required|string|max:20',
            'equip_id' => 'required|string|max:20',
            'equip_type' => 'required|string|max:20',
            'note_type_id' => ['required', 'numeric', Rule::exists('note_types', 'id')],
            'defect_id' => ['required', 'numeric', Rule::exists('defects', 'id')],
            'cause_id' => ['required', 'numeric', Rule::exists('causes', 'id')],
            'solution_id' => ['required', 'numeric', Rule::exists('solutions', 'id')],
            'services' => 'required|string|max:1300',
            'date' => 'required|date_format:d/m/Y',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'first_tec' => 'required|exists:tecs,id',
            'sign_t_1' => 'required|string', // base64 signature
            'finished' => 'required|boolean',
            
            // Optional time fields
            'go_start' => 'nullable|date_format:H:i',
            'go_end' => 'nullable|date_format:H:i',
            'back_start' => 'nullable|date_format:H:i',
            'back_end' => 'nullable|date_format:H:i',
            
            // Optional fields
            'second_tec' => 'nullable|exists:tecs,id|different:first_tec',
            'sign_t_2' => 'nullable|string', // base64 signature
            'sign_cl' => 'nullable|string', // base64 signature
            
            // Client information
            'cl_name' => 'nullable|string|max:40',
            'cl_function' => 'nullable|string|max:40',
            'cl_contact' => 'nullable|string|max:40',
            
            // Time validations
            'go_start' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if ($this->go_end && $value && strtotime($value) > strtotime($this->go_end)) {
                        $fail('O horário de saída (Ida) deve ser anterior ao horário de chegada (Ida).');
                    }
                },
            ],
            'start' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if ($this->end && strtotime($value) > strtotime($this->end)) {
                        $fail('O horário de início deve ser anterior ao horário de término.');
                    }
                },
            ],
            'back_start' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if ($this->back_end && $value && strtotime($value) > strtotime($this->back_end)) {
                        $fail('O horário de saída (Retorno) deve ser anterior ao horário de chegada (Retorno).');
                    }
                },
            ],
            
            // Materials validation
            'materials' => 'sometimes|array',
            'materials.*.material_id' => [
                'required_with:materials',
                'exists:materials,id',
                // If you need to validate against session IDs:
                // Rule::in(session('materials_ids', []))
            ],
            'materials.*.quantity' => 'required_with:materials.*.material_id|numeric|min:1|max:9999',
            
            // Kilometer validations
            // 'km_start' => 'nullable|numeric|min:0|max:999999',
            // 'km_end' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     'max:999999',
            //     function ($attribute, $value, $fail) {
            //         if ($this->km_start && $value && $value < $this->km_start) {
            //             $fail('O quilometragem final deve ser maior ou igual à quilometragem inicial.');
            //         }
            //     },
            // ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'order_id' => 'ordem de serviço',
            'equip_mod' => 'modelo do equipamento',
            'equip_id' => 'ID do equipamento',
            'equip_type' => 'tipo do equipamento',
            'note_type_id' => 'tipo de serviço',
            'defect_id' => 'defeito',
            'cause_id' => 'causa',
            'solution_id' => 'solução',
            'services' => 'serviços executados',
            'first_tec' => 'primeiro técnico',
            'second_tec' => 'segundo técnico',
            'sign_t_1' => 'assinatura do técnico 1',
            'sign_t_2' => 'assinatura do técnico 2',
            'sign_cl' => 'assinatura do cliente',
            'cl_name' => 'nome do cliente',
            'cl_function' => 'função do cliente',
            'cl_contact' => 'contato do cliente',
            'finished' => 'finalizado',
            'materials.*.material_id' => 'ID do material',
            'materials.*.quantity' => 'quantidade',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            // Required fields
            'required' => 'O campo :attribute é obrigatório.',
            'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
            
            // Date and time formats
            'date_format' => 'O campo :attribute deve estar no formato :format.',
            
            // String length
            'max' => 'O campo :attribute não deve ter mais de :max caracteres.',
            
            // Numeric validations
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute deve ser pelo menos :min.',
            'max.numeric' => 'O campo :attribute não deve ser maior que :max.',
            
            // Existence validations
            'exists' => 'O :attribute selecionado é inválido.',
            
            // Boolean
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
            
            // Relations
            'different' => 'O segundo técnico deve ser diferente do primeiro técnico.',
            
            // Array validation
            'array' => 'O campo :attribute deve ser um array.',
            
            // Custom messages for specific fields
            'date.date_format' => 'Data inválida. Use o formato DD/MM/YYYY.',
            'go_start.date_format' => 'Horário de saída inválido (Ida). Use o formato HH:MM.',
            'go_end.date_format' => 'Horário de chegada inválido (Ida). Use o formato HH:MM.',
            'start.date_format' => 'Horário de início inválido. Use o formato HH:MM.',
            'end.date_format' => 'Horário de término inválido. Use o formato HH:MM.',
            'back_start.date_format' => 'Horário de saída inválido (Retorno). Use o formato HH:MM.',
            'back_end.date_format' => 'Horário de chegada inválido (Retorno). Use o formato HH:MM.',
            
            // Materials validation messages
            'materials.*.material_id.required_with' => 'O ID do material é obrigatório.',
            'materials.*.material_id.exists' => 'O material selecionado é inválido.',
            'materials.*.quantity.required_with' => 'A quantidade do material é obrigatória.',
            'materials.*.quantity.min' => 'Não possível registrar materiais com quantidade menor que :min.',
            'materials.*.quantity.max' => 'Não é possível registrar materiais com quantidade maior que :max.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'go_start' => $this->go_start ?: null,
            'go_end' => $this->go_end ?: null,
            'back_start' => $this->back_start ?: null,
            'back_end' => $this->back_end ?: null,
            'second_tec' => $this->second_tec ?: null,
            'sign_t_2' => $this->sign_t_2 ?: null,
            'sign_cl' => $this->sign_cl ?: null,
            'cl_name' => $this->cl_name ?: null,
            'cl_function' => $this->cl_function ?: null,
            'cl_contact' => $this->cl_contact ?: null,
            'km_start' => $this->km_start ?: null,
            'km_end' => $this->km_end ?: null,
            'finished' => filter_var($this->finished, FILTER_VALIDATE_BOOLEAN),
            
            // Validate materials array structure
            'materials' => $this->has('materials') && is_array($this->materials) 
                ? array_filter($this->materials, function($material) {
                    return !empty($material['material_id']) && isset($material['quantity']);
                })
                : null,
        ]);
    }

    /**
     * Get validated data with additional processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Parse date from d/m/Y to Y-m-d format for database
        if (isset($validated['date'])) {
            $validated['date'] = Carbon::createFromFormat('d/m/Y', $validated['date'])->format('Y-m-d');
        }
        
        return $validated;
    }
}