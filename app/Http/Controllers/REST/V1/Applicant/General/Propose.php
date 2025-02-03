<?php

namespace App\Http\Controllers\REST\V1\Applicant\General;

use App\Http\Controllers\REST\BaseREST;
use App\Http\Controllers\REST\Errors;

class Propose extends BaseREST
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
        'SCHOLARSHIP_GENERAL_PROPOSE'
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

        // Make sure applicant has confirm application
        if (!DBRepo::checkApplicantById($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'Application id not found')
                    ->setReportId('AGP1')
            );
        }

        // Make sure application status is DRAFT
        if (!DBRepo::isApplicationStatusDraft($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Application status is not DRAFT')
                    ->setReportId('AGP2')
            );
        }

        return $this->update();
    }

    /** 
     * Function to update data 
     * @return object
     */
    public function update()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        $update = $dbRepo->propose();

        if ($update->status) {

            if (env('APP_ENV') == 'local') {
                return $this->respond(200, $update->data ?? null);
            }

            return $this->respond(200);
        }

        return $this->error(500, [
            'reason' => $update->message
        ]);
    }
}
