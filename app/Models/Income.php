<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'value', 'date'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $perPage = 10;
}
