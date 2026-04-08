<?php

namespace App\Http\Requests;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormNoteRequest extends FormRequest
{
    public function rules(): array
    {
        // Base rules for non-material fields
        $rules = [
            'equip_mod' => 'required|max:20',
            'equip_id' => 'required|max:20',
            'equip_type' => 'required|max:20',
            'note_type_id' => ['required', 'numeric', Rule::exists('note_types', 'id')],
            'defect_id' => ['required', 'numeric', Rule::exists('defects', 'id',)],
            'cause_id' => ['required', 'numeric', Rule::exists('causes', 'id')],
            'solution_id' => ['required', 'numeric', Rule::exists('solutions', 'id')],
            'services' => 'required|max:1300',
            'date' => 'required|date_format:Y-m-d',
            'go_start' => 'nullable|date_format:H:i',
            'go_end' => 'nullable|date_format:H:i',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'back_start' => 'nullable|date_format:H:i',
            'back_end' => 'nullable|date_format:H:i',
            'obs' => 'max:40',
            'first_tec' => 'required',
            'sign_t_1' => 'required',
            'cl_name' => 'max:40',
            'cl_function' => 'max:40',
            'cl_contact' => 'max:40',
            'finished' => 'required',
            'material_ids_array' => 'nullable|regex:/^\d+(,\d+)*$/', // Validate materialsList
        ];

        // Check if materialsList is not empty
        if (!empty($this->input('material_ids_array'))) {
            // Get the list of material IDs from the request
            $materialsList = $this->input('material_ids_array', '');
            $materialIds = array_filter(explode(',', $materialsList)); // Split into array and remove empty values

            // Add dynamic rules for each material
            foreach ($materialIds as $index => $materialId) {
                $rules["material_id_{$materialId}"] = ['required', 'numeric', Rule::in(session('materials_ids'))];
                $rules["material_id_{$materialId}_qtd"] = 'required|numeric|min:1';
            }
        }
        return $rules;
    }

    public function messages(): array
    {
        // Base messages for non-material fields
        $messages = [
            'equip_mod.required' => 'Digite o modelo do equipamento',
            'equip_mod.max' => 'Modelo do equipamento não deve ter mais de 20 caracteres',
            'equip_id.max' => 'ID do equipamento não deve ter mais de 20 caracteres',
            'equip_id.required' => 'Digite o ID do equipamento',
            'equip_type.required' => 'Digite o tipo do equipamento',
            'equip_type.max' => 'Tipo do equipamento não deve ter mais de 20 caracteres',
            'note_type_id.required' => 'Selecione um tipo de serviço',
            'note_type_id.numeric' => 'Selecione um tipo de serviço válido',
            'note_type_id.exists' => 'Selecione um tipo de serviço válido',
            'defect_id.required' => 'Selecione o defeito',
            'defect_id.numeric' => 'Selecione o defeito',
            'defect_id.exists' => 'Selecione um defeito válido',
            'cause_id.required' => 'Selecione uma possível causa',
            'cause_id.numeric' => 'Selecione uma possível causa',
            'cause_id.exists' => 'Selecione uma possível causa válida',
            'solution_id.required' => 'Selecione a solução executada',
            'solution_id.numeric' => 'Selecione a solução executada',
            'solution_id.exists' => 'Selecione uma solução válida',
            'services.required' => 'Descreva os serviços executados',
            'services.max' => 'A descricão dos serviços não podem ter mais de 1300 caracteres',
            'date.required' => 'Selecione a data',
            'date.date_format' => 'Data inválida',
            'go_start.date_format' => 'Horário de saída inválido (Ida)',
            'go_end.date_format' => 'Horário de chegada inválido (Ida)',
            'start.required' => 'Selecione o horário de Início',
            'start.date_format' => 'Horário de início inválido',
            'end.required' => 'Selecione o horário de término',
            'end.date_format' => 'Horário de término inválido',
            'back_start.date_format' => 'Horário de saída inválido (Retorno)',
            'back_end.date_format' => 'Horário de chegada inválido (Retorno)',
            'material_ids_array.regex' => 'Algum caractere não numérico foi encontrado na lista de IDs de materiais.', // Custom message for regex validation
        ];

        // Check if materialsList is not empty
        if (!empty($this->input('material_ids_array'))) {
            // Get the list of material IDs from the request
            $materialsList = $this->input('material_ids_array', '');
            $materialIds = array_filter(explode(',', $materialsList)); // Split into array and remove empty values

            // Add dynamic messages for each material
            foreach ($materialIds as $index => $materialId) {
                $messages["material_id_{$materialId}.required"] = "O sistema não identificou o material ID {$materialId}.";
                $messages["material_id_{$materialId}.numeric"] = "O ID do material {$materialId} encontrado não é numérico.";

                if (!in_array($materialId, session('materials_ids'))) {
                    $material = Material::withTrashed()->find($materialId);
                    $messages["material_id_{$materialId}.in"] = "Material {$material->completeDescription()} não encontrado no sistema,  verifique se não foi desabilitado pela adminstração.";
                }
               
                $messages["material_id_{$materialId}_qtd.required"] = "Quantidade para o material ID {$materialId} não encontrada.";
                $messages["material_id_{$materialId}_qtd.numeric"] = "Quantidade encontrada para o material ID {$materialId} não é numérica.";
                $messages["material_id_{$materialId}_qtd.min"] = "Quantidade para o material ID {$materialId} não pode ser menor que 1.";
            }
        }

        return $messages;
    }
}