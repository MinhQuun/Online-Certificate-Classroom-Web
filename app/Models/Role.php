<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $table = 'quyen';
    protected $primaryKey = 'maQuyen';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'maQuyen',
        'tenQuyen',
        'moTa',
    ];

    /**
     * Normalize role name to a lowercase slug for comparisons.
     */
    public function slug(): string
    {
        return Str::slug($this->tenQuyen ?? '');
    }
}