<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class ScheduleModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tsc_createdAt';
    const UPDATED_AT = 'tsc_updatedAt';

    protected $primaryKey = 'tsc_id';
    protected $table = 'schedule';
    protected $fillable = [
        'tsc_shiftCode',
        'tsc_productionDate',
        'tsc_expiredDate',
        'tsc_expiredCode',
        'tpr_id',
        'tm_id',
    ];
    protected $hidden = [
        'tsc_createdAt',
        'tsc_updatedAt',
    ];

    /**
     * Privilege from relation to pivot tables
     */
    public function scheduleEmployee()
    {
        return $this->belongsToMany(EmployeeModel::class, 'schedule_employee', 'tsc_id', 'te_id');
    }

    /**
     * Relation with table machine
     */
    public function machine()
    {
        return $this->belongsTo(MachineModel::class, 'tm_id');
    }

    /**
     * Relation with table product
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'tpr_id');
    }
}
