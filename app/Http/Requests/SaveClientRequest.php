<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveClientRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->method() == "POST" ? 
               $this->hasPrivilege("INSCLNT") :
               $this->hasPrivilege("UPDCLNT");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "acquirer_type_code" => "required",
            "user_id"            => "nullable",
            "name"               => "required|max:150",
            "official_name"      => "max:250",            
            "location"           => "max:50",
            "rfc"                => "max:20",
            "address"            => "max:300",
            "email"              => "required|max:80",
            "phone"              => "required|max:15",
            "status"             => "required"
        ];
    }
}
