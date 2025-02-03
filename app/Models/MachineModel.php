<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class MachineModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tm_createdAt';
    const UPDATED_AT = 'tm_updatedAt';

    protected $primaryKey = 'tm_id';
    protected $table = 'machine';
    protected $fillable = [
        'tm_code',
        'tm_name',
    ];
    protected $hidden = [
        'tm_createdAt',
        'tm_updatedAt',
    ];
}
