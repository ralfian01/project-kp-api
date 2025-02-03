<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class StudentModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'ts_createdAt';
    const UPDATED_AT = 'ts_updatedAt';

    protected $primaryKey = 'ts_id';
    protected $table = 'student';
    protected $fillable = [
        'ta_id',
        'ts_studentNumber',
        'ts_university',
        'tsml_id',
        'ts_majorLevel',
        'ts_majorName',
        'ts_scienceType',
        'ts_GPA',
        'ts_studentCardPhoto',
        'ts_studyPlanCard',
        'ts_studyResultCard',
    ];
    protected $hidden = [
        'ts_createdAt',
        'ts_updatedAt'
    ];

    /**
     * Relation with table role
     */
    public function account()
    {
        return $this->belongsTo(AccountModel::class, 'ta_id');
    }

    /**
     * Relation with table role
     */
    public function majorLevel()
    {
        return $this->belongsTo(StudentMajorLevelModel::class, 'tsml_id');
    }

    /**
     * Get account with its privileges
     */
    protected function scopeGetWithAccount(Builder $query)
    {
        return $query
            ->with(['account'])
            ->addSelect(['ta_id'])
            ->get()
            ->map(function ($acc) {

                $acc->makeHidden(['account']);

                return $acc;
            });
    }
}
