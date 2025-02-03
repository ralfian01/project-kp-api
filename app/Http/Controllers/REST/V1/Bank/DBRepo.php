<?php

namespace App\Http\Controllers\REST\V1\Bank;

use App\Http\Libraries\BaseDBRepo;
use App\Models\BankModel;
use Exception;

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
                BankModel::select([
                    "tb_id as id",
                    "tb_code as code",
                    "tb_name as name",
                ])
                ->where('tb_statusActive', true);

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
}
