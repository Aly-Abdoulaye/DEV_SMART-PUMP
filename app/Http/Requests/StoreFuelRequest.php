<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFuelRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }
    
    public function rules()
    {
        $companyId = auth()->user()->company_id;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fuels')->where('company_id', $companyId)
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fuels')->where('company_id', $companyId)
            ],
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500'
        ];
    }
    
    public function messages()
    {
        return [
            'name.unique' => 'Un carburant avec ce nom existe déjà dans votre entreprise.',
            'code.unique' => 'Ce code est déjà utilisé dans votre entreprise.'
        ];
    }
}