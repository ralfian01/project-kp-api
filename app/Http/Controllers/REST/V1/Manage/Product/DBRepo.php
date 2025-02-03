<?php

namespace App\Http\Controllers\REST\V1\Manage\Product;

use App\Http\Libraries\BaseDBRepo;
use App\Models\ProductModel;
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
     * Function to check product id
     * @return bool
     */
    public static function checkProductId($bankId)
    {
        return ProductModel::find($bankId) != null;
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

            $data = ProductModel::select([
                "tpr_id as id",
                "tpr_name as name",
                "tpr_weight as weight",
                "tpr_expired as expired_duration",
                "tpr_imagePath as image",
            ]);

            // Filter by id
            if (isset($this->payload['id'])) {
                $data = $data->where('tpr_id', $this->payload['id']);
            } else {
                // Filter by product name
                if (isset($this->payload['name'])) {
                    $data = $data->where('tpr_name', 'LIKE', "%{$this->payload['name']}%");
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

                if (isset($this->file['image']))
                    $this->payload['image'] = $this->file['image']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tpr_name' => $this->payload['name'] ?? null,
                    'tpr_weight' => $this->payload['weight'] ?? null,
                    'tpr_expired' => $this->payload['expired_duration'] ?? null,
                    'tpr_imagePath' => $this->payload['image'] ?? $this->payload['image_url'] ?? null,
                ]);

                ## Insert valid region
                $insertData = ProductModel::create($dbPayload);

                if (!$insertData) {
                    $tableName = ProductModel::tableName();
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
                    'tpr_name' => $this->payload['name'] ?? null,
                    'tpr_weight' => $this->payload['weight'] ?? null,
                    'tpr_expired' => $this->payload['expired_duration'] ?? null,
                    'tpr_imagePath' => $this->payload['image'] ?? null,
                ]);

                ## Update data
                $updateData = ProductModel::find($this->payload['id'])->update($dbPayload);

                if (!$updateData) {
                    $tableName = ProductModel::tableName();
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
                $deleteData = ProductModel::find($this->payload['id'])->delete();

                if (!$deleteData) {
                    $tableName = ProductModel::tableName();
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
