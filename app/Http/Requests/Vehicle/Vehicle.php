<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\Request;

class Vehicle extends Request
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
            'name'              =>  'required',
            'contact_msisdn'    =>  'required',
            'vehicle_type_id'   =>  'required|exists:vehicle_types,id',
            'license_no'        =>  'required',
            'brand'             =>  'required',
            'model'             =>  'required',
            'latitude'          =>  'required',
            'longitude'         =>  'required',
            'status'            =>  'required'
        ];
    }
}
