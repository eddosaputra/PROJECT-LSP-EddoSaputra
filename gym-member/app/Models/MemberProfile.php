<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberProfile extends Model
{
    protected $fillable = [
        'user_id',
        'birth_date',
        'height',
        'weight',
        'photo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}