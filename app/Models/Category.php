<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $perPage = 10;

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }
}
