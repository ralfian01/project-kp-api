<?php

namespace App\Http\Controllers\REST\V1\Manage\Complain;

use App\Http\Controllers\REST\BaseREST;
use App\Http\Controllers\REST\Errors;

class Update extends BaseREST
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
        'product_id' => '',
        'complain_number' => '',
        'expired_code' => 'regex:/^\d{8}-\d{1}$/',
        'complain_category' => '',
        'description' => '',
        'receive_media' => '',
        'complain_date' => '',
        'product_status' => '',
        'evicende_file' => 'file|mime:jpeg,jpg,png'
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'COMPLAIN_MANAGE_VIEW',
        'COMPLAIN_MANAGE_MODIFY',
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

        // Make sure complain id is available
        if (!DBRepo::checkComplainId($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'Complain id not found')
                    ->setReportId('MCU1')
            );
        }

        // Make sure product id is available
        if (isset($this->payload['product_id'])) {
            if (!DBRepo::checkProductId($this->payload['product_id'])) {
                return $this->error(
                    (new Errors)
                        ->setMessage(409, 'Product id not available')
                        ->setReportId('MCU2')
                );
            }
        }

        return $this->update();
    }

    /** 
     * Function to insert data 
     * @return object
     */
    public function update()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        $update = $dbRepo->updateData();

        if ($update->status) {
            return $this->respond(200);
        }

        return $this->error(500, [
            'reason' => $update->message
        ]);
    }
}
