<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SaveUserRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->method() == "POST" ? 
               $this->hasPrivilege("INSUSR") :
               $this->hasPrivilege("UPDUSR");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "role_code"   => "required",
            "code"        => [
                "required",
                "max:15",
                Rule::unique("users")->ignore($this->route("user")) 
            ],
            "name"        => "required|max:80",
            "birthday"    => "required",
            "description" => "max:250",
            "email"       => [
                "required",
                "max:80",
                Rule::unique("users")->ignore($this->route("user")) 
            ],
            "phone"       => "max:15",
            "password"    => $this->route("user") ? "max:12" : "required|max:12",
            "status"      => "required",
        ];
    }
}
