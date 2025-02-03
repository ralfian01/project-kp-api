<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class ProductModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tpr_createdAt';
    const UPDATED_AT = 'tpr_updatedAt';

    protected $primaryKey = 'tpr_id';
    protected $table = 'product';
    protected $fillable = [
        'tpr_name',
        'tpr_weight',
        'tpr_expired',
        'tpr_imagePath'
    ];
    protected $hidden = [
        'tpr_createdAt',
        'tpr_updatedAt',
    ];
}
