<?php

namespace App\Http\Requests\City;

use App\Http\Requests\Request;

class Update extends Request
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
            'name'      =>  'required|max:64',
            'state_id'  =>  'required|exists:states,id',
            'status'    =>  'required'
        ];
    }
}
