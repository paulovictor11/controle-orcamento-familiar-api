<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'value', 'date', 'category_id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['category'];
    protected $perPage = 10;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCategoryAttribute(): string
    {
        $category = Category::find($this->category_id);
        return $category->name;
    }
}
