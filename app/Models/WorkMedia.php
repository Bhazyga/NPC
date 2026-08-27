<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'type',
        'path',
        'title',
        'alt_text',
        'caption',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'sort_order'  => 'integer',
        'is_featured' => 'boolean',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
