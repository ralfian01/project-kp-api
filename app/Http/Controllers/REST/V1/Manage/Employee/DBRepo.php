<?php

namespace App\Http\Controllers\REST\V1\Manage\Employee;

use App\Http\Libraries\BaseDBRepo;
use App\Models\EmployeeModel;
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
     * Function to check employee id
     * @return bool
     */
    public static function checkEmployeeId($id)
    {
        return EmployeeModel::find($id) != null;
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

            $data = EmployeeModel::select([
                "te_id as id",
                "te_name as name",
                "te_statusActive as status_active",
            ]);

            if (isset($this->payload['id'])) {
                $data = $data->where('te_id', $this->payload['id']);
            } else {
                // Filter by name
                if (isset($this->payload['name'])) {
                    $data = $data->where('te_name', 'LIKE', "%{$this->payload['name']}%");
                }
            }


            $data = $data->get();

            return (object) [
                'status' => !$data->isEmpty(),
                'data' => $data->isEmpty()
                    ? null
                    : (isset($this->payload['id'])
                        ? $data->toArray()[0]
                        : $data->toArray())
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
                    'te_name' => $this->payload['name'] ?? null,
                    'te_statusActive' => $this->payload['status_active'] ?? null,
                ]);

                ## Insert valid region
                $insertData = EmployeeModel::create($dbPayload);

                if (!$insertData) {
                    $tableName = EmployeeModel::tableName();
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
                    'te_name' => $this->payload['name'] ?? null,
                    'te_statusActive' => $this->payload['status_active'] ?? null,
                ]);

                ## Update data
                $updateData = EmployeeModel::find($this->payload['id'])->update($dbPayload);

                if (!$updateData) {
                    $tableName = EmployeeModel::tableName();
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
                $deleteData = EmployeeModel::find($this->payload['id'])->delete();

                if (!$deleteData) {
                    $tableName = EmployeeModel::tableName();
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
