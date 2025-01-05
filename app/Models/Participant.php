<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'image',
        'weight',
        'waist',
        'hip',
        'chest',
        'pushups',
        'pullups',
        'weights_lifted',
        'sprint_time',
        'before_photo',
        'after_photo',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'waist' => 'decimal:2',
        'hip' => 'decimal:2',
        'chest' => 'decimal:2',
        'weights_lifted' => 'decimal:2',
        'sprint_time' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

