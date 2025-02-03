<?php

namespace App\Models;

use App\Models\IndonesianRegion\CityModel;
use App\Models\IndonesianRegion\DistrictModel;
use App\Models\IndonesianRegion\ProvinceModel;
use App\Models\IndonesianRegion\VillageModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static mixed getWithPrivileges() Get account with its privileges
 * @method mixed getWithPrivileges() Get account with its privileges
 */
class IdentityModel extends Model
{
    use HasFactory;

    const CREATED_AT = 'tid_createdAt';
    const UPDATED_AT = 'tid_updatedAt';

    protected $primaryKey = 'tid_id';
    protected $table = 'identity';
    protected $fillable = [
        'ta_id',
        'tid_fullName',
        'tid_phoneNumber',
        'tid_idCardNumber',
        'tid_idCardPhoto',
        'tid_idCardProvince',
        'tid_idCardCity',
        'tid_idCardDistrict',
        'tid_idCardVillage',
        'tid_idCardPostalCode',
        'tid_idCardAddress',
        'tid_famCardNumber',
        'tid_famCardPhoto',
        'tid_domicileProvince',
        'tid_domicileCity',
        'tid_domicileDistrict',
        'tid_domicileVillage',
        'tid_domicilePostalCode',
        'tid_domicileAddress',
        'tid_coorLatitude',
        'tid_coorLongitude',
        'tid_bankNumber',
        'tid_bankName',
        'tid_bankBookPhoto',
    ];
    protected $hidden = [
        'tid_createdAt',
        'tid_updatedAt'
    ];

    /**
     * Relation with table role
     */
    public function account()
    {
        return $this->belongsTo(AccountModel::class, 'ta_id');
    }

    public function provinceIdCard()
    {
        return $this->belongsTo(ProvinceModel::class, 'tid_idCardProvince', 'tipr_id',);
    }

    public function cityIdCard()
    {
        return $this->belongsTo(CityModel::class, 'tid_idCardCity', 'tict_id');
    }

    public function districtIdCard()
    {
        return $this->belongsTo(DistrictModel::class, 'tid_idCardDistrict', 'tidt_id');
    }

    public function villageIdCard()
    {
        return $this->belongsTo(VillageModel::class, 'tid_idCardVillage', 'tivl_id');
    }

    public function provinceDomicile()
    {
        return $this->belongsTo(ProvinceModel::class, 'tid_domicileProvince', 'tipr_id',);
    }

    public function cityDomicile()
    {
        return $this->belongsTo(CityModel::class, 'tid_domicileCity', 'tict_id');
    }

    public function districtDomicile()
    {
        return $this->belongsTo(DistrictModel::class, 'tid_domicileDistrict', 'tidt_id');
    }

    public function villageDomicile()
    {
        return $this->belongsTo(VillageModel::class, 'tid_domicileVillage', 'tivl_id');
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
