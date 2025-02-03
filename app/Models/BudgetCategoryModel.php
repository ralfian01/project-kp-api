<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class BudgetCategoryModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tbgc_createdAt';
    const UPDATED_AT = 'tbgc_updatedAt';

    protected $primaryKey = 'tbgc_id';
    protected $table = 'budget__category';
    protected $fillable = [
        'tbgc_code',
        'tbgc_name',
    ];
    protected $hidden = [
        'tbgc_createdAt',
        'tbgc_updatedAt',
    ];
}
