<?php

namespace App\Http\Requests;

use App\Models\Work;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workParam = $this->route('work');
        $workId = is_object($workParam)
            ? $workParam->id
            : (Work::where('slug', $workParam)->value('id') ?? Work::where('id', $workParam)->value('id') ?? $workParam);

        return [
            'num'               => ['sometimes', 'nullable', 'string', 'max:50'],
            'title'             => ['sometimes', 'required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('works', 'slug')->ignore($workId)],
            'category'          => ['sometimes', 'required', 'string', 'max:255'],
            'year'              => ['sometimes', 'required', 'string', 'max:50'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'client'            => ['nullable', 'string', 'max:255'],
            'color'             => ['nullable', 'string', 'max:50'],
            'featured'          => ['nullable', 'boolean'],
            'is_published'      => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0', 'max:9999'],
            'youtube_url'       => ['nullable', 'url', 'max:500', 'regex:/youtube\.com|youtu\.be/'],
            'seo_title'         => ['nullable', 'string', 'max:255'],
            'seo_description'   => ['nullable', 'string', 'max:500'],

            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['string', 'max:100'],

            'tag_ids'     => ['nullable', 'array'],
            'tag_ids.*'   => ['integer', 'exists:tags,id'],

            'service_ids'   => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],

            'links'              => ['nullable', 'array'],
            'links.*.id'         => ['nullable', 'integer', 'exists:links,id'],
            'links.*.label'      => ['required_with:links', 'string', 'max:255'],
            'links.*.url'        => ['required_with:links', 'url', 'max:500'],
            'links.*.type'       => ['nullable', 'string', 'max:100'],
            'links.*.sort_order' => ['nullable', 'integer', 'min:0'],

            // Image file uploads up to 20MB (20480 KB)
            'cover_image_file'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:20480'],
            'hero_image_file'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:20480'],
            'gallery_images'     => ['nullable', 'array', 'max:30'],
            'gallery_images.*'   => ['image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:20480'],
            'gallery_titles'     => ['nullable', 'array'],
            'gallery_titles.*'   => ['nullable', 'string', 'max:255'],
            'gallery_alts'       => ['nullable', 'array'],
            'gallery_alts.*'     => ['nullable', 'string', 'max:255'],
            'gallery_captions'   => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string'],
        ];
    }
}
