<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_category_id',
        'title',
        'image',
        'status',
    ];

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }
}
