<?php

namespace App\Http\Controllers\REST\V1\My\Application;

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
        'scholarship_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'gpa' => '',
        'university' => '',
        'major_level' => '',
        'major_name' => '',
        'science_type' => 'in:EXACT,NON_EXACT',
        'student_number' => '',
        'student_card_photo' => 'file|mimes:jpg,jpeg,png,pdf',
        'study_plan_card' => 'file|mimes:jpg,jpeg,png,pdf',
        'study_result_card' => 'file|mimes:jpg,jpeg,png,pdf',
        'not_civil_servant' => 'file|mimes:jpg,jpeg,png,pdf',
        'pddikti' => 'file|mimes:jpg,jpeg,png,pdf',
        'active_college' => 'file|mimes:jpg,jpeg,png,pdf',
    ];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [
        'APPLICANT_SCHOLARSHIP_DRAFT'
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

        // Make sure application status is DRAFT
        if (!DBRepo::isApplicationStatusDraft($this->auth['account_id'])) {
            return $this->error(
                (new Errors)
                    ->setMessage(403, 'Application cannot be edited')
                    ->setReportId('MAD1')
            );
        }

        // Make sure the chosen bank is available
        if (isset($this->payload['bank_name'])) {
            if (!DBRepo::checkBankAvailability($this->payload['bank_name'])) {
                return $this->error(
                    (new Errors)
                        ->setMessage(409, 'The chosen bank is not available')
                        ->setReportId('MAD2')
                );
            }
        }

        // # Make sure id domicile regions (province, city, district, village) is valid
        $checkDomicile = DBRepo::checkRegionId(...[
            'province_id' => $this->payload['domicile_province'] ?? null,
            'city_id' => $this->payload['domicile_city'] ?? null,
            'district_id' => $this->payload['domicile_district'] ?? null,
            'village_id' => $this->payload['domicile_village'] ?? null,
        ]);
        if ($checkDomicile->province !== null && !$checkDomicile->province) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of domicile_province')
                    ->setReportId('MAD3')
            );
        }

        if ($checkDomicile->city !== null && !$checkDomicile->city) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of domicile_city')
                    ->setReportId('MAD4')
            );
        }

        if ($checkDomicile->district !== null && !$checkDomicile->district) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of domicile_district')
                    ->setReportId('MAD5')
            );
        }

        if ($checkDomicile->village !== null && !$checkDomicile->village) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of domicile_village')
                    ->setReportId('MAD6')
            );
        }


        // # Make sure id id card regions (province, city, district, village) is valid
        $checkIdCard = DBRepo::checkRegionId(...[
            'province_id' => $this->payload['id_card_province'] ?? null,
            'city_id' => $this->payload['id_card_city'] ?? null,
            'district_id' => $this->payload['id_card_district'] ?? null,
            'village_id' => $this->payload['id_card_village'] ?? null,
        ]);
        if ($checkIdCard->province !== null && !$checkIdCard->province) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of id_card_province')
                    ->setReportId('MAD7')
            );
        }

        if ($checkIdCard->city !== null && !$checkIdCard->city) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of id_card_city')
                    ->setReportId('MAD8')
            );
        }

        if ($checkIdCard->district !== null && !$checkIdCard->district) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of id_card_district')
                    ->setReportId('MAD9')
            );
        }

        if ($checkIdCard->village !== null && !$checkIdCard->village) {
            return $this->error(
                (new Errors)
                    ->setMessage(409, 'Invalid id of id_card_village')
                    ->setReportId('MAD10')
            );
        }

        // # Make sure province_id and city_id is a validated id to receive the scholarship
        if (isset($this->payload['domicile_province'], $this->payload['city_province'])) {
            if (!DBRepo::checkValidRegion($this->payload['domicile_province'], $this->payload['city_province'])) {
                return $this->error(
                    (new Errors)
                        ->setMessage(409, 'domicile_province or domicile_city is not a valid region')
                        ->setReportId('MAD7')
                );
            }
        }


        // // # Make sure major level id is valid
        // if (isset($this->payload['major_level'])) {
        //     if (!DBRepo::checkMajorLevelId($this->payload['major_level'])) {
        //         return $this->error(
        //             (new Errors)
        //                 ->setMessage(409, 'major_level is invalid')
        //                 ->setReportId('MAD8')
        //         );
        //     }
        // }

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
