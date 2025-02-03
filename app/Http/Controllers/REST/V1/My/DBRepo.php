<?php

namespace App\Http\Controllers\REST\V1\My;

use App\Http\Libraries\BaseDBRepo;
use App\Models\AccountModel;
use App\Models\ApplicantModel;
use App\Models\IndonesianRegion\CityModel;
use App\Models\IndonesianRegion\DistrictModel;
use App\Models\IndonesianRegion\ProvinceModel;
use App\Models\IndonesianRegion\VillageModel;
use App\Models\PrivilegeModel;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Psy\Readline\Hoa\Console;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    // public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    // {
    //     parent::__construct($payload, $file, $auth);
    // }

    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check application
     * @return bool
     */
    public static function checkApplicantId($applicantId)
    {
        return ApplicantModel::find($applicantId);
    }

    /**
     * Function to check application status
     * @return bool
     */
    public static function checkApplicationStatus($applicantId, $status = "PENDING")
    {
        $data =
            ApplicantModel::where('tapp_id', $applicantId)
            ->where('tapp_status', $status)
            ->get();

        return !$data->isEmpty();
    }


    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /** 
     * Function to get data from database
     * @return array|null|object
     */
    public function getUserData()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            $data = AccountModel::find($this->auth['account_id']);
            $data->makeHidden(['ta_id', 'tr_id', 'ta_deletable', 'ta_statusActive', 'ta_statusDelete', 'ta_uuid', 'ta_username']);
            $data->username = $data->ta_username;
            $data->uuid = $data->ta_uuid;

            // AccountModel::with([
            //     'accountPrivilege',
            //     'accountRole.rolePrivilege'
            // ])
            // // ->select(['ta_id', 'tr_id'])
            // ->where('ta_id', $this->auth['account_id'])
            // ->get()
            // ->map(function ($acc) {

            //     $acc->makeHidden(['accountPrivilege', 'accountRole', 'ta_id', 'tr_id']);

            //     if (isset($acc->accountPrivilege)) {
            //         $acc->privileges = $acc->accountPrivilege->map(function ($prv) {
            //             return $prv->tp_code;
            //         })->toArray();
            //     }

            //     if (isset($acc->accountRole->rolePrivilege)) {
            //         $acc->privileges = array_unique(
            //             $acc->accountRole->rolePrivilege->map(function ($prv) {
            //                 return $prv->tp_code;
            //             })->toArray()
            //         );
            //     }

            //     return $acc->privileges;
            // });

            return (object) [
                'status' => $data != null,
                'data' => $data->toArray()
            ];
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /** 
     * Function to get data from database
     * @return array|null|object
     */
    public function getPrivileges()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            $data =
                AccountModel::with(['accountPrivilege', 'accountRole.rolePrivilege'])
                ->select(['ta_id', 'tr_id'])
                ->where('ta_id', $this->auth['account_id'])
                ->get()
                ->map(function ($acc) {

                    $acc->makeHidden(['accountPrivilege', 'accountRole', 'ta_id', 'tr_id']);

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

                    return $acc->privileges;
                });

            return (object) [
                'status' => !$data->isEmpty(),
                'data' => $data->isEmpty() ? null : $data->first()
            ];
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
