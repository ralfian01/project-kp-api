<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivilegeModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tp_createdAt';
    const UPDATED_AT = 'tp_updatedAt';

    protected $primaryKey = 'tp_id';
    protected $table = 'privilege';
    protected $fillable = ['tp_code', 'tp_description'];
    protected $hidden = ['tp_createdAt', 'tp_updatedAt'];
}
