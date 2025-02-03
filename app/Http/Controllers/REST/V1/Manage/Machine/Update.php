<?php

namespace App\Http\Controllers\REST\V1\Manage\Machine;

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
        'code' => '',
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'MACHINE_MANAGE_VIEW',
        'MACHINE_MANAGE_MODIFY',
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

        // Make sure machine id is available
        if (!DBRepo::checkMachineId($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'Machine id not found')
                    ->setReportId('MMU1')
            );
        }

        // Make sure machine code not duplicate
        if (isset($this->payload['code'])) {
            if (!DBRepo::checkMachineCodeDuplicate($this->payload['code'], $this->payload['id'])) {
                return $this->error(
                    (new Errors)
                        ->setMessage(409, 'Machine code already used')
                        ->setReportId('MMU2')
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
