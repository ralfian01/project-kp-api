<?php

namespace App\Http\Controllers\REST\V1\Manage\Employee;

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
        'name' => '',
        'status_active' => 'boolean'
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'EMPLOYEE_MANAGE_VIEW',
        'EMPLOYEE_MANAGE_MODIFY',
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

        // Make sure employee id is available
        if (!DBRepo::checkEmployeeId($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'Employee id not found')
                    ->setReportId('MEU1')
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
