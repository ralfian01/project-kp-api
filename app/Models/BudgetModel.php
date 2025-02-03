<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class BudgetModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tbg_createdAt';
    const UPDATED_AT = 'tbg_updatedAt';

    protected $primaryKey = 'tbg_id';
    protected $table = 'budget';
    protected $fillable = [
        'tbgc_id',
        'tbg_year',
        'tbg_amount',
        'tbg_periodStart',
        'tbg_periodEnd',
        'tbg_statusActive',
    ];
    protected $hidden = [
        'tbg_createdAt',
        'tbg_updatedAt',
    ];

    /**
     * Relation with table budget category
     */
    public function budgetCategory()
    {
        return $this->belongsTo(BudgetCategoryModel::class, 'tbgc_id');
    }

    /**
     * Relation with table budget allocation
     */
    public function budgetAllocation()
    {
        return $this->hasMany(BudgetAllocationModel::class, 'tbg_id', 'tbg_id');
    }
}
