<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('works', 'slug')],
            'num'               => ['nullable', 'string', 'max:10'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'client'            => ['nullable', 'string', 'max:255'],
            'year'              => ['nullable', 'string', 'max:10', 'regex:/^\d{4}$/'],
            'category'          => ['nullable', 'string', 'max:255'],
            'image'             => ['nullable', 'string', 'max:500'],
            'cover_image'       => ['nullable', 'string', 'max:500'],
            'hero_image'        => ['nullable', 'string', 'max:500'],
            'color'             => ['nullable', 'string', 'max:30', 'regex:/^#[0-9A-Fa-f]{3,8}$/'],
            'youtube_url'       => ['nullable', 'url', 'max:500', 'regex:/youtube\.com|youtu\.be/'],
            'seo_title'         => ['nullable', 'string', 'max:255'],
            'seo_description'   => ['nullable', 'string', 'max:500'],
            'is_published'      => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'featured'          => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0', 'max:9999'],
            'tags'              => ['nullable', 'array'],   // legacy json tags

            'tag_ids'     => ['nullable', 'array'],
            'tag_ids.*'   => ['integer', 'exists:tags,id'],

            'service_ids'   => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],

            'links'              => ['nullable', 'array'],
            'links.*.label'      => ['required_with:links', 'string', 'max:255'],
            'links.*.url'        => ['required_with:links', 'url', 'max:500'],
            'links.*.type'       => ['nullable', 'string', 'max:100'],
            'links.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
