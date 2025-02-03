<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class ComplainModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tc_createdAt';
    const UPDATED_AT = 'tc_updatedAt';

    protected $primaryKey = 'tc_id';
    protected $table = 'complain';
    protected $fillable = [
        'tc_number',
        'tc_expiredCode',
        'tc_category',
        'tc_description',
        'tc_receiveMedia',
        'tc_date',
        'tc_productStatus',
        'tc_evidencePath',
        'tpr_id',
    ];
    protected $hidden = [
        'tc_createdAt',
        'tc_updatedAt',
    ];

    /**
     * Relation with table product
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'tpr_id');
    }
}
