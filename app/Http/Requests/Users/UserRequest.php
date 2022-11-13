<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        } elseif ($this->isMethod('put')) {
            return $this->updateRules();
        }
    }

    /**
     * Define validation rules to store method for resource creation
     *
     * @return array
     */
    public function createRules(): array
    {
        return [
            'type'     => 'required|in:Administrador,Encargado',
            'name'     => 'required|string|max:191',
            'username' => 'required|string|min:5|max:20|unique:users',
            'email'    => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:5'
        ];
    }

    /**
     * Define validation rules to update method for resource update
     *
     * @return array
     */
    public function updateRules(): array
    {
        return [
            'type'     => 'required|in:Administrador,Encargado',
            'name'     => 'sometimes|string|max:191',
            'username' => 'sometimes|string|min:5|max:20|unique:users,username,' . $this->get('id'),
            'email'    => 'sometimes|string|email|max:191|unique:users,email,' . $this->get('id')
        ];
    }
}
