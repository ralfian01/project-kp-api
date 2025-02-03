<?php

namespace App\Http\Controllers\REST\V1\Manage\Complain;

use App\Http\Libraries\BaseDBRepo;
use App\Models\ComplainModel;
use App\Models\EmployeeModel;
use App\Models\MachineModel;
use App\Models\ProductModel;
use App\Models\ScheduleEmployeeModel;
use App\Models\ScheduleModel;
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
     * Function to check schedule id
     * @return bool
     */
    public static function checkComplainId($id)
    {
        return ComplainModel::find($id) != null;
    }

    /**
     * Function to check product id
     * @return bool
     */
    public static function checkProductId($id)
    {
        return ProductModel::find($id) != null;
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

            $data =
                ComplainModel::with([
                    'product' => function ($query) {
                        return $query->select(
                            'tpr_id',
                            'tpr_id as id',
                            'tpr_name as name',
                            'tpr_weight as weight',
                            'tpr_expired as expired',
                            'tpr_imagePath as image',
                        );
                    },
                ])
                ->select([
                    'tpr_id',
                    'tc_id as id',
                    'tc_number as complain_number',
                    'tc_expiredCode as expired_code',
                    'tc_category as complain_category',
                    'tc_description as description',
                    'tc_receiveMedia as receive_media',
                    'tc_date as complain_date',
                    'tc_productStatus as product_status',
                    'tc_evidencePath as evidence_file',
                ]);

            // ## Filter by complain id
            if (isset($this->payload['id'])) {
                $data = $data->where('tc_id', $this->payload['id']);
            } else {
                // ## Filter by complain_date
                if (isset($this->payload['complain_date'])) {
                    $data = $data->where('tc_date', $this->payload['complain_date']);
                }

                // ## Filter by keyword
                if (isset($this->payload['keyword'])) {
                    $data = $data->where(function ($query) {
                        $query
                            ->where('tc_number', 'LIKE', "%{$this->payload['keyword']}%")
                            ->orWhereHas('product', function ($subQuery) {
                                $subQuery->where('tpr_name', 'LIKE', "%{$this->payload['keyword']}%");
                            });
                    });
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

                if (isset($this->file['evidence_image']))
                    $this->payload['evidence_image'] = $this->file['evidence_image']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tc_number' => $this->payload['complain_number'] ?? null,
                    'tc_expiredCode' => $this->payload['expired_code'] ?? null,
                    'tc_category' => $this->payload['complain_category'] ?? null,
                    'tc_description' => $this->payload['description'] ?? null,
                    'tc_receiveMedia' => $this->payload['receive_media'] ?? null,
                    'tc_date' => $this->payload['complain_date'] ?? null,
                    'tc_productStatus' => $this->payload['product_status'] ?? null,
                    'tpr_id' => $this->payload['product_id'] ?? null,
                    'tc_evidencePath' => $this->payload['evidence_image'] ?? null,
                ]);

                ## Insert schedule
                $insertData = ComplainModel::create($dbPayload);

                if (!$insertData) {
                    $tableName = ComplainModel::tableName();
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

                if (isset($this->file['evidence_image']))
                    $this->payload['evidence_image'] = $this->file['evidence_image']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tc_number' => $this->payload['complain_number'] ?? null,
                    'tc_expiredCode' => $this->payload['expired_code'] ?? null,
                    'tc_category' => $this->payload['complain_category'] ?? null,
                    'tc_description' => $this->payload['description'] ?? null,
                    'tc_receiveMedia' => $this->payload['receive_media'] ?? null,
                    'tc_date' => $this->payload['complain_date'] ?? null,
                    'tc_productStatus' => $this->payload['product_status'] ?? null,
                    'tpr_id' => $this->payload['product_id'] ?? null,
                    'tc_evidencePath' => $this->payload['evidence_image'] ?? null,
                ]);

                ## Update data
                $updateData = ComplainModel::find($this->payload['id'])->update($dbPayload);

                if (!$updateData) {
                    $tableName = ComplainModel::tableName();
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
                $deleteData = ComplainModel::find($this->payload['id'])->delete();

                if (!$deleteData) {
                    $tableName = ComplainModel::tableName();
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
