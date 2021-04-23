<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\Request;

class Driver extends Request
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
        return [
            'name'                  =>  'required|max:64',
            'photo'                 =>  'sometimes|image',
            'contact_msisdn'        =>  'required|max:32',
            // 'date_of_birth'         =>  'required|date',
            'driving_license_no'    =>  'required|max:64',
            'reference_name'        =>  'required|max:64',
            'reference_msisdn'      =>  'required|max:32',
            'status'                =>  'required'
        ];
    }
}
