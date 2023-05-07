<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SaveProductRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->method() == "POST" ? 
               $this->hasPrivilege("INSPROD") :
               $this->hasPrivilege("UPDPROD");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // "unit_id"             => "required",
            // "product_category_id" => "required",
            // "product_type_code"   => "required",
            "code"                => [
                "required",
                "max:15",
                Rule::unique("products")->ignore($this->route("product")) 
            ],
            "name"                => "required|max:150",
            "description"         => "max:500",
            "price_list"          => "required",
            "flex_price"          => "nullable",
            "status"              => "required"
        ];
    }
}
