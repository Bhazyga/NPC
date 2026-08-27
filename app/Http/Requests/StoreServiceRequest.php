<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('services', 'slug')],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'hero_image'        => ['nullable', 'string', 'max:500'],
            'youtube_url'       => ['nullable', 'url', 'max:500', 'regex:/youtube\.com|youtu\.be/'],
            'seo_title'         => ['nullable', 'string', 'max:255'],
            'seo_description'   => ['nullable', 'string', 'max:500'],
            'is_published'      => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0', 'max:9999'],

            // Image file uploads up to 20MB (20480 KB)
            'hero_image_file'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:20480'],
            'gallery_images'     => ['nullable', 'array', 'max:30'],
            'gallery_images.*'   => ['image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:20480'],
            'gallery_titles'     => ['nullable', 'array'],
            'gallery_titles.*'   => ['nullable', 'string', 'max:255'],
            'gallery_alts'       => ['nullable', 'array'],
            'gallery_alts.*'     => ['nullable', 'string', 'max:255'],
            'gallery_captions'   => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string'],

            // Service items
            'items'                   => ['nullable', 'array'],
            'items.*.title'           => ['required_with:items', 'string', 'max:255'],
            'items.*.description'     => ['nullable', 'string'],
            'items.*.sort_order'      => ['nullable', 'integer', 'min:0'],

            // Tags
            'tag_ids'   => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],

            // Related works
            'work_ids'   => ['nullable', 'array'],
            'work_ids.*' => ['integer', 'exists:works,id'],

            // External links
            'links'              => ['nullable', 'array'],
            'links.*.label'      => ['required_with:links', 'string', 'max:255'],
            'links.*.url'        => ['required_with:links', 'url', 'max:500'],
            'links.*.type'       => ['nullable', 'string', 'max:100'],
            'links.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
