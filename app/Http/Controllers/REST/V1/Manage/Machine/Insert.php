<?php

namespace App\Http\Controllers\REST\V1\Manage\Machine;

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
        'name' => 'required',
        'code' => 'required',
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'MACHINE_MANAGE_VIEW',
        'MACHINE_MANAGE_ADD',
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

        // Make sure machine code not duplicate
        if (!DBRepo::checkMachineCodeDuplicate($this->payload['code'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Machine code already used')
                    ->setReportId('MMI1')
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
