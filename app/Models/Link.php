<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'linkable_type',
        'linkable_id',
        'label',
        'url',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function linkable()
    {
        return $this->morphTo();
    }
}
