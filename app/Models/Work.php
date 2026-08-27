<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        // original fields
        'num',
        'title',
        'category',
        'year',
        'description',
        'tags',        // json — legacy; normalized tags via work_tag pivot
        'image',
        'color',
        'featured',
        // new fields
        'slug',
        'short_description',
        'client',
        'cover_image',
        'hero_image',
        'youtube_url',
        'seo_title',
        'seo_description',
        'is_published',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'tags'         => 'array',
        'featured'     => 'boolean',
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
        'sort_order'   => 'integer',
    ];

    // ── Route model binding by slug ──────────────────────
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (self $work) {
            if (empty($work->slug) && !empty($work->title)) {
                $work->slug = Str::slug($work->title);
            }
        });
    }

    // ── Relationships ────────────────────────────────────

    public function media()
    {
        return $this->hasMany(WorkMedia::class)->orderBy('sort_order');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_work')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function normalizedTags()
    {
        return $this->belongsToMany(Tag::class, 'work_tag');
    }

    public function links()
    {
        return $this->morphMany(Link::class, 'linkable')->orderBy('sort_order');
    }

    // ── Helper: safe YouTube embed URL ──────────────────
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        return Service::parseYoutubeUrl($this->youtube_url);
    }
}
