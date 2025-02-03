<?php

namespace App\Http\Controllers\REST\V1\Applicant\General;

use App\Http\Controllers\REST\BaseREST;
use App\Http\Controllers\REST\Errors;

class Draft extends BaseREST
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
        'recommender_name' => '',
        'full_name' => '',
        'phone_number' => '',
        'id_card_number' => '',
        'id_card_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'id_card_province' => '',
        'id_card_city' => '',
        'id_card_district' => '',
        'id_card_village' => '',
        'id_card_address' => '',
        'id_card_postal_code' => '',
        'id_card_as_domicile' => 'boolean',
        'family_card_number' => '',
        'family_card_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'domicile_province' => '',
        'domicile_city' => '',
        'domicile_district' => '',
        'domicile_village' => '',
        'domicile_address' => '',
        'domicile_postal_code' => '',
        'coor_lat' => '',
        'coor_long' => '',
        'bank_name' => '',
        'bank_number' => '',
        'bank_book_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'house_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'pass_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'scholarship_photo' => 'file|mimes:pdf',
        'gpa' => '',
        'university' => '',
        'major_level' => '',
        'major_name' => '',
        'science_type' => 'in:EXACT,NON_EXACT',
        'student_number' => '',
        'student_card_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'study_plan_card' => 'file|mimes:pdf',
        'study_result_card' => 'file|mimes:pdf',
        'not_civil_servant' => 'file|mimes:pdf',
        'pddikti' => 'file|mimes:pdf',
        'active_college' => 'file|mimes:pdf',
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'SCHOLARSHIP_GENERAL_DRAFT'
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

        // Make sure application is available
        if (!DBRepo::checkApplicantById($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(404, 'application id not found')
                    ->setReportId('AGD1')
            );
        }

        // Make sure application status is DRAFT
        if (!DBRepo::isApplicationStatusDraft($this->payload['id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(403, 'Application cannot be edited')
                    ->setReportId('AGD2')
            );
        }

        // Make sure the chosen bank is available
        if (isset($this->payload['bank_name'])) {
            if (!DBRepo::checkBankAvailability($this->payload['bank_name'])) {
                return $this->error(
                    (new Errors)
                        ->setMessage(409, 'The chosen bank is not available')
                        ->setReportId('AGD3')
                );
            }
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

        $update = $dbRepo->updateData();

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
