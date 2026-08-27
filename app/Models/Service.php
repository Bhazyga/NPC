<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'hero_image',
        'youtube_url',
        'seo_title',
        'seo_description',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    // ── Route model binding by slug ──────────────────────
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Auto-generate slug from title ───────────────────
    protected static function booted(): void
    {
        static::creating(function (self $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    // ── Relationships ────────────────────────────────────

    public function items()
    {
        return $this->hasMany(ServiceItem::class)->orderBy('sort_order');
    }

    public function media()
    {
        return $this->hasMany(ServiceMedia::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'service_tag');
    }

    public function works()
    {
        return $this->belongsToMany(Work::class, 'service_work')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function links()
    {
        return $this->morphMany(Link::class, 'linkable')->orderBy('sort_order');
    }

    // ── Helper: safe YouTube embed URL ──────────────────
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        return self::parseYoutubeUrl($this->youtube_url);
    }

    public static function parseYoutubeUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        // youtu.be/ID
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }
        // youtube.com/shorts/ID
        if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }
        // youtube.com/watch?v=ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }

        return null;
    }
}
