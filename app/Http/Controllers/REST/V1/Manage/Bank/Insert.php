<?php

namespace App\Http\Controllers\REST\V1\Manage\Bank;

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
        'bank_name' => 'required',
        'bank_code' => 'required',
        'status' => 'required|in:ACTIVE,INACTIVE',
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'BANK_MANAGE_ADD'
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

        // Make sure valid region is not duplicated
        if (!DBRepo::checkBankCodeDuplicate($this->payload['bank_code'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'bank_code already used')
                    ->setReportId('MBI1')
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
