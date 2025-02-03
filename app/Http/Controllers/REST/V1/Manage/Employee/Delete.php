<?php

namespace App\Http\Controllers\REST\V1\Manage\Employee;

use App\Http\Controllers\REST\BaseREST;
use App\Http\Controllers\REST\Errors;

class Delete extends BaseREST
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
    protected $payloadRules = [];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
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

        // Make sure product id is available
        if (!DBRepo::checkEmployeeId($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'Employee id not found')
                    ->setReportId('MED1')
            );
        }

        return $this->delete();
    }

    /** 
     * Function to delete data 
     * @return object
     */
    public function delete()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        $delete = $dbRepo->deleteData();

        if ($delete->status) {
            return $this->respond(200);
        }

        return $this->error(500, [
            'reason' => $delete->message
        ]);
    }
}
