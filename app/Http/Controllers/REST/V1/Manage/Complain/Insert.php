<?php

namespace App\Http\Controllers\REST\V1\Manage\Complain;

use App\Http\Controllers\REST\BaseREST;
use App\Http\Controllers\REST\Errors;

class Insert extends BaseREST
{
    public function __construct(
        ?array $payload = [],
        ?array $file = [],
        ?array $auth = [],
    ) {

        $this->payload = $payload;
        $this->file = $file;
        $this->auth = $auth;
        return $this;
    }

    /**
     * @var array Property that contains the payload rules
     */
    protected $payloadRules = [
        'product_id' => 'required',
        'complain_number' => 'required',
        'expired_code' => 'required|regex:/^\d{8}-\d{1}$/',
        'complain_category' => 'required',
        'description' => '',
        'receive_media' => 'required',
        'complain_date' => 'required',
        'product_status' => 'required',
        'evicende_file' => 'file|mime:jpeg,jpg,png'
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'COMPLAIN_MANAGE_VIEW',
        'COMPLAIN_MANAGE_ADD',
    ];


    /**
     * The method that starts the main activity
     * @return null
     */
    protected function mainActivity()
    {
        return $this->nextValidation();
    }

    /**
     * Handle the next step of payload validation
     * @return void
     */
    private function nextValidation()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        // Make sure product id is available
        if (!DBRepo::checkProductId($this->payload['product_id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Product id not available')
                    ->setReportId('MCI1')
            );
        }

        return $this->insert();
    }

    /** 
     * Function to insert data 
     * @return object
     */
    public function insert()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        $insert = $dbRepo->insertData();

        if ($insert->status) {
            return $this->respond(200);
        }

        return $this->error(500, [
            'reason' => $insert->message
        ]);
    }
}
