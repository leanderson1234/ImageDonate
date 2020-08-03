<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateUserRequest extends FormRequest
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
        $id = $this->segment(4);
        return [
            'name' => "required|min:3|max:20",
            'email' => "required|email:rfc,dns|unique:users,email,{$id},id",
            'image_path' => "image",
            "password" => "required|min:3|max:20"
           
        ];
    }
}
