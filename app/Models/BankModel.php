<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class BankModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tb_createdAt';
    const UPDATED_AT = 'tb_updatedAt';

    protected $primaryKey = 'tb_id';
    protected $table = 'bank';
    protected $fillable = [
        'tb_code',
        'tb_name',
        'tb_statusActive',
    ];
    protected $hidden = [
        'tb_createdAt',
        'tb_updatedAt',
    ];
}
