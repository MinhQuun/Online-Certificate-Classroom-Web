<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestMaterial extends Model
{
    protected $table = 'test_tailieu';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maTest',
        'tenTL',
        'loai',
        'mime_type',
        'visibility',
        'public_url',
    ];

    public function courseTest(): BelongsTo
    {
        return $this->belongsTo(CourseTest::class, 'maTest', 'maTest');
    }
}

