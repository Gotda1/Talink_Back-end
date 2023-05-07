<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->hasPrivilege("INSORDR");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "client_id"           => "required",
            "observations"        => "max:500",
            "taxes"               => "nullable",
            "payment_method_code" => "nullable",
            "amount"              => "min:0",
            "observations"        => "max:500",
            "warranty"            => "required|max:200",
            "advance_payment"     => "required",
        ];
    }
}
