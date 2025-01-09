<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name','description', 'owner_id', 'duration_days', 'contribution','visibility','icon'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}