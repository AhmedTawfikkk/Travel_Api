<?php

namespace App\Http\Requests;


use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TourListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return[
            'pricefrom'=>'numeric',
            'priceto'=>'numeric',
            'datefrom'=>'date',
            'dateto'=>'date',
            'sortby'=>Rule::in('price'),
            'sortorder'=>Rule::in(['asc','desc'])

        ];
    }

    public function messages()
    {
        return [
            'sortby'=>"the 'sortby' parameter accepts 'price' only'",
            'sortorder'=>"the 'sortorder' parameter accepts 'asc' or 'desc' only'"
        ];
    }
}
