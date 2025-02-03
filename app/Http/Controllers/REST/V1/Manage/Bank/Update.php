<?php

namespace App\Http\Controllers\REST\V1\Manage\Bank;

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
        'bank_name' => '',
        'bank_code' => '',
        'status' => 'in:ACTIVE,INACTIVE',
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'BANK_MANAGE_MODIFY'
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

        // Make sure bank id is available
        if (!DBRepo::checkBankId($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'Bank id not found')
                    ->setReportId('MBU1')
            );
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
