<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveArticleRequest extends FormRequest
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
            'data.type' => ['required', 'in:articles'],
            'data.id' => [
                Rule::requiredIf($this->route('article')),
                'exists:articles,slug',
            ],
            'data.attributes.title' => ['required', 'min:4'],
            'data.attributes.slug' => [
                'required',
                'alpha_dash',
                new Slug(),
                Rule::unique('articles', 'slug')->ignore($this->route('article')),
            ],
            'data.attributes.content' => ['required'],
            'data.relationships.category.data.id' => [
                Rule::requiredIf(! $this->route('article')),
                Rule::exists('categories', 'slug'),
            ],
            'data.relationships.author.data.id' => [
                Rule::requiredIf(! $this->route('article')),
                Rule::exists('users', 'id'),
            ],
        ];
    }
}
