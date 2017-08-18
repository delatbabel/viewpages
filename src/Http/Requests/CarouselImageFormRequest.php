<?php

namespace Delatbabel\ViewPages\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate Request for Carousel Image form
 *
 * Class CarouselImageFormRequest
 */
class CarouselImageFormRequest extends FormRequest
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
            'carousels'       => 'required',
            'name'            => 'required',
            'path'            => 'required_without_all:use_html,path_original|mimes:jpg,jpeg,jpe,png,gif',
            'url'             => 'url',
            'displaying_time' => 'required|numeric',
            'html'            => 'required_with:use_html'

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
            'path' => 'image',
            'url'  => 'link'
        ];
    }
}
