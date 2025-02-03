<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class BudgetAllocationModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tbga_createdAt';
    const UPDATED_AT = 'tbga_updatedAt';

    protected $primaryKey = 'tbga_id';
    protected $table = 'budget__allocation';
    protected $fillable = [
        'tbg_id',
        'tsml_id',
        'tbga_amount',
        'tbga_quota',
    ];
    protected $hidden = [
        'tbga_createdAt',
        'tbga_updatedAt',
    ];

    /**
     * Relation with table budget
     */
    public function budget()
    {
        return $this->belongsTo(BudgetModel::class, 'tbg_id');
    }

    /**
     * Relation with table budget
     */
    public function majorLevel()
    {
        return $this->belongsTo(StudentMajorLevelModel::class, 'tsml_id');
    }
}
