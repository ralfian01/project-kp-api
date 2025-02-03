<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class ScheduleEmployeeModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tse_createdAt';
    const UPDATED_AT = 'tse_updatedAt';

    protected $primaryKey = 'tse_id';
    protected $table = 'schedule_employee';
    protected $fillable = [
        'tsc_id',
        'te_id',
    ];
    protected $hidden = [
        'tse_createdAt',
        'tse_updatedAt',
    ];
}
