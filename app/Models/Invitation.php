<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitations';

    protected $fillable = [
        'invitation_id',
        'name',
        'delegation',
        'attendance',
        'message',
    ];
}
