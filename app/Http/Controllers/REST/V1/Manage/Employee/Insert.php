<?php

namespace App\Http\Controllers\REST\V1\Manage\Employee;

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
        'status_active' => 'boolean'
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'EMPLOYEE_MANAGE_VIEW',
        'EMPLOYEE_MANAGE_ADD',
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
