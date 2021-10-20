<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'walk_id',
        'org_name',
        'name'
    ];

    public function walk()
    {
        return $this->belongsTo(Walk::class);
    }
}
