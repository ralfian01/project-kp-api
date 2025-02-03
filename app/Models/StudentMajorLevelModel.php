<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 */
class StudentMajorLevelModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tsml_createdAt';
    const UPDATED_AT = 'tsml_updatedAt';

    protected $primaryKey = 'tsml_id';
    protected $table = 'student__major_level';
    protected $fillable = [
        'tsml_code',
        'tsml_name',
    ];
    protected $hidden = [
        'tsml_createdAt',
        'tsml_updatedAt',
    ];
}
