<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'danhmuc';
    protected $primaryKey = 'maDanhMuc';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenDanhMuc',
        'slug',
        'icon',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'maDanhMuc', 'maDanhMuc');
    }
}

