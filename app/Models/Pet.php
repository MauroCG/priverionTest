<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    /**
     * The mass assignable attributes
     */
    protected $fillable = [
        'name', 'photo_filename', 'inside_house'
    ];
}
