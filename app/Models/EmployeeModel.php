<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class EmployeeModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'te_createdAt';
    const UPDATED_AT = 'te_updatedAt';

    protected $primaryKey = 'te_id';
    protected $table = 'employee';
    protected $fillable = [
        'te_name',
        'te_statusActive',
    ];
    protected $hidden = [
        'te_createdAt',
        'te_updatedAt',
    ];
}
