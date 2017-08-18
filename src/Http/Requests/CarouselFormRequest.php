<?php

namespace Delatbabel\ViewPages\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate Request for Carousel form
 *
 * Class CarouselFormRequest
 */
class CarouselFormRequest extends FormRequest
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
        $rule = [
            'key'  => 'required',
            'name' => 'required',
            'end_date' => 'after:start_date'
        ];

        return $rule;
    }

    /**
     * Custom nice name
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'key' => 'carousel location',
        ];
    }
}
