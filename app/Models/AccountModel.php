<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class AccountModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'ta_createdAt';
    const UPDATED_AT = 'ta_updatedAt';

    protected $primaryKey = 'ta_id';
    protected $table = 'account';
    protected $fillable = [
        'ta_uuid',
        'ta_username',
        'ta_password',
        'tr_id',
        'ta_statusActive',
        'ta_statusDelete',
    ];
    protected $hidden = [
        'ta_createdAt',
        'ta_updatedAt',
        'ta_password'
    ];

    /**
     * Relation with table role
     */
    public function accountRole()
    {
        return $this->belongsTo(RoleModel::class, 'tr_id');
    }

    /**
     * Privilege from relation between account, account__privilege and privilege tables
     */
    public function accountPrivilege()
    {
        return $this->belongsToMany(PrivilegeModel::class, 'account__privilege', 'ta_id', 'tp_id');
    }

    /**
     * Get account with its privileges
     */
    protected function scopeGetWithPrivileges(Builder $query)
    {
        return $query
            ->with(['accountPrivilege', 'accountRole.rolePrivilege'])
            ->addSelect(['ta_id', 'tr_id'])
            ->get()
            ->map(function ($acc) {

                $acc->makeHidden(['accountPrivilege', 'accountRole']);

                if (isset($acc->accountPrivilege)) {
                    $acc->privileges = $acc->accountPrivilege->map(function ($prv) {
                        return $prv->tp_code;
                    })->toArray();
                }

                if (isset($acc->accountRole->rolePrivilege)) {
                    $acc->privileges = array_unique(
                        $acc->accountRole->rolePrivilege->map(function ($prv) {
                            return $prv->tp_code;
                        })->toArray()
                    );
                }

                return $acc;
            });
    }
}
