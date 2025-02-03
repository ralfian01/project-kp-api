<?php

namespace App\Http\Controllers\REST\V1\Manage\Machine;

use App\Http\Libraries\BaseDBRepo;
use App\Models\MachineModel;
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
     * Function to check machine id
     * @return bool
     */
    public static function checkMachineId($id)
    {
        return MachineModel::find($id) != null;
    }

    /**
     * Function to check machine code duplication
     * @return bool
     */
    public static function checkMachineCodeDuplicate($code, $id = null)
    {
        $machine =
            MachineModel::where('tm_code', $code)
            ->where('tm_id', '!=', $id)
            ->get();

        return $machine->isEmpty();
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

            $data = MachineModel::select([
                "tm_id as id",
                "tm_name as name",
                "tm_code as code",
            ]);


            // Filter by id
            if (isset($this->payload['id'])) {
                $data = $data->where('tm_id', $this->payload['id']);
            } else {
                // Filter by machine name
                if (isset($this->payload['name'])) {
                    $data = $data->where('tm_name', 'LIKE', "%{$this->payload['name']}%");
                }

                // Filter by machine code
                if (isset($this->payload['code'])) {
                    $data = $data->where('tm_code', 'LIKE', "%{$this->payload['code']}%");
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
                    'tm_code' => $this->payload['code'] ?? null,
                    'tm_name' => $this->payload['name'] ?? null,
                ]);

                ## Insert valid region
                $insertData = MachineModel::create($dbPayload);

                if (!$insertData) {
                    $tableName = MachineModel::tableName();
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
                    'tm_code' => $this->payload['code'] ?? null,
                    'tm_name' => $this->payload['name'] ?? null,
                ]);

                ## Update data
                $updateData = MachineModel::find($this->payload['id'])->update($dbPayload);

                if (!$updateData) {
                    $tableName = MachineModel::tableName();
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
                $deleteData = MachineModel::find($this->payload['id'])->delete();

                if (!$deleteData) {
                    $tableName = MachineModel::tableName();
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
