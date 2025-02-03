<?php

namespace App\Http\Controllers\REST\V1\Manage\Bank;

use App\Http\Libraries\BaseDBRepo;
use App\Models\BankModel;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
     * Function to check bank id
     * @return bool
     */
    public static function checkBankId($bankId)
    {
        return BankModel::find($bankId) != null;
    }

    /**
     * Function to check application status
     * @return bool
     */
    public static function checkBankCodeDuplicate($bankCode)
    {
        $bank = BankModel::where('tb_code', $bankCode)->get();

        return $bank->isEmpty();
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
    public function getData()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            $data = BankModel::select('*');

            if (isset($this->payload['bank_id'])) {
                $data = $data->where('tb_id', $this->payload['bank_id']);
            }

            if (isset($this->payload['bank_name'])) {
                $data = $data->where('tb_name', 'LIKE', "%{$this->payload['bank_name']}%");
            }

            $data = $data->get();

            return (object) [
                'status' => !$data->isEmpty(),
                'data' => $data->isEmpty() ? null : $data->toArray()
            ];
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Function to insert data from database
     * @return object|bool
     */
    public function insertData()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            return DB::transaction(function () {

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tb_code' => $this->payload['bank_code'] ?? null,
                    'tb_name' => $this->payload['bank_name'] ?? null,
                    'tb_statusActive' => $this->payload['status'] ? $this->payload['status'] == "ACTIVE" : null,
                ]);

                ## Insert valid region
                $insertData = BankModel::create($dbPayload);

                if (!$insertData) {
                    $tableName = BankModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }

                // Return transaction status
                return (object) [
                    'status' => true,
                ];
            });
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Function to update data from database
     * @return object|bool
     */
    public function updateData()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            return DB::transaction(function () {

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tb_code' => $this->payload['bank_code'] ?? null,
                    'tb_name' => $this->payload['bank_name'] ?? null,
                    'tb_statusActive' => $this->payload['status'] ? $this->payload['status'] == "ACTIVE" : null,
                ]);

                ## Update data
                $updateData = BankModel::find($this->payload['id'])->update($dbPayload);

                if (!$updateData) {
                    $tableName = BankModel::tableName();
                    throw new Exception("Failed when update data into table \"{$tableName}\"");
                }

                // Return transaction status
                return (object) [
                    'status' => true,
                ];
            });
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Function to insert data from database
     * @return object|bool
     */
    public function deleteData()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            return DB::transaction(function () {

                ## Delete valid region
                $deleteData = BankModel::find($this->payload['id'])->delete();

                if (!$deleteData) {
                    $tableName = BankModel::tableName();
                    throw new Exception("Failed when delete data into table \"{$tableName}\"");
                }

                // Return transaction status
                return (object) [
                    'status' => true,
                ];
            });
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
