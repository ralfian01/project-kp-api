<?php

namespace App\Http\Controllers\REST\V1\Applicant\Collab;

use App\Http\Libraries\BaseDBRepo;
use App\Models\ApplicantModel;
use App\Models\IdentityModel;
use App\Models\StudentModel;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    // public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    // {
    //     parent::__construct($payload, $file, $auth);
    // }

    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check application
     * @return bool
     */
    public static function checkApplication($account_id)
    {
        $data = ApplicantModel::where('ta_id', $account_id)->get();

        return !$data->isEmpty();
    }

    /**
     * Function to check application status
     * @return bool
     */
    public static function checkApplicationStatus($account_id, $status = "DRAFT")
    {
        $data =
            ApplicantModel::where('ta_id', $account_id)
            ->where('tapp_status', $status)
            ->get();

        return !$data->isEmpty();
    }


    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /**
     * Function to update data from database
     * @return object|bool
     */
    public function updateData()
    {
        ## Formatting additional data which not payload
        // ## Get identity id
        $identity =
            IdentityModel::select('tid_id AS identity_id')
            ->where('ta_id', $this->auth['account_id'])
            ->first();

        // ## Get student id
        $student =
            StudentModel::select('ts_id AS student_id')
            ->where('ta_id', $this->auth['account_id'])
            ->first();

        // ## Get applicant id
        $applicant =
            ApplicantModel::select('tapp_id AS applicant_id')
            ->where('ta_id', $this->auth['account_id'])
            ->first();


        ## Formatting payload
        // ## Formatting domicile address
        if (isset($this->payload['id_card_as_domicile']) && $this->payload['id_card_as_domicile']) {
            $this->payload['domicile_province'] = $this->payload['id_card_province'];
            $this->payload['domicile_city'] = $this->payload['id_card_city'];
            $this->payload['domicile_district'] = $this->payload['id_card_district'];
            $this->payload['domicile_village'] = $this->payload['id_card_village'];
            $this->payload['domicile_postal_code'] = $this->payload['id_card_postal_code'];
            $this->payload['domicile_address'] = $this->payload['id_card_address'];
        }
        try {

            return DB::transaction(function () use ($identity, $student, $applicant) {

                // ## Save file
                if (isset($this->file['id_card_photo']))
                    $this->payload['id_card_photo'] = $this->file['id_card_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['family_card_photo']))
                    $this->payload['family_card_photo'] = $this->file['family_card_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['bank_book_photo']))
                    $this->payload['bank_book_photo'] = $this->file['bank_book_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tid_fullName' => $this->payload['full_name'] ?? null,
                    'tid_phoneNumber' => $this->payload['phone_number'] ?? null,
                    'tid_idCardNumber' => $this->payload['id_card_number'] ?? null,
                    'tid_idCardPhoto' => $this->payload['id_card_photo'] ?? null,
                    'tid_idCardProvince' => $this->payload['id_card_province'] ?? null,
                    'tid_idCardCity' => $this->payload['id_card_city'] ?? null,
                    'tid_idCardDistrict' => $this->payload['id_card_district'] ?? null,
                    'tid_idCardVillage' => $this->payload['id_card_village'] ?? null,
                    'tid_idCardPostalCode' => $this->payload['id_card_postal_code'] ?? null,
                    'tid_idCardAddress' => $this->payload['id_card_address'] ?? null,
                    'tid_domicileProvince' => $this->payload['domicile_province'] ?? null,
                    'tid_domicileCity' => $this->payload['domicile_city'] ?? null,
                    'tid_domicileDistrict' => $this->payload['domicile_district'] ?? null,
                    'tid_domicileVillage' => $this->payload['domicile_village'] ?? null,
                    'tid_domicilePostalCode' => $this->payload['domicile_postal_code'] ?? null,
                    'tid_domicileAddress' => $this->payload['domicile_address'] ?? null,
                    'tid_famCardNumber' => $this->payload['family_card_number'] ?? null,
                    'tid_famCardPhoto' => $this->payload['family_card_photo'] ?? null,
                    'tid_famCardNumber' => $this->payload['family_card_number'] ?? null,
                    'tid_coorLatitude' => $this->payload['coor_lat'] ?? null,
                    'tid_coorLongitude' => $this->payload['coor_long'] ?? null,
                    'tid_bankName' => $this->payload['bank_name'] ?? null,
                    'tid_bankNumber' => $this->payload['bank_number'] ?? null,
                    'tid_bankBookPhoto' => $this->payload['bank_book_photo'] ?? null,
                ]);

                ## Update/Insert identity data
                if (!$identity) {
                    $dbPayload['ta_id'] = $this->auth['account_id'];

                    $insertIdentity = IdentityModel::create($dbPayload);
                } else {
                    $insertIdentity = IdentityModel::find($identity->identity_id)->update($dbPayload);
                }

                if (!$insertIdentity) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }


                if (isset($this->file['student_card_photo']))
                    $this->payload['student_card_photo'] = $this->file['student_card_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['study_plan_card']))
                    $this->payload['study_plan_card'] = $this->file['study_plan_card']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['study_result_card']))
                    $this->payload['study_result_card'] = $this->file['study_result_card']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                ## Insert student data
                $studentData = Arr::whereNotNull([
                    'ts_studentNumber' => $this->payload['student_number'] ?? null,
                    'ts_studentCardPhoto' => $this->payload['student_card_photo'] ?? null,
                    'ts_university' => $this->payload['university'] ?? null,
                    'ts_majorLevel' => $this->payload['major_level'] ?? null,
                    'ts_majorName' => $this->payload['major_name'] ?? null,
                    'ts_scienceType' => $this->payload['science_type'] ?? null,
                    'ts_GPA' => $this->payload['gpa'] ?? null,
                    'ts_studyPlanCard' => $this->payload['study_plan_card'] ?? null,
                    'ts_studyResultCard' => $this->payload['study_result_card'] ?? null,
                ]);

                if (!$student) {
                    $studentData['ta_id'] = $this->auth['account_id'];

                    $insertStudent = StudentModel::insert($studentData);
                } else {
                    $insertStudent = StudentModel::find($student->student_id)->update($studentData);
                }

                if (!$insertStudent) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }



                if (isset($this->file['pass_photo']))
                    $this->payload['pass_photo'] = $this->file['pass_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['house_photo']))
                    $this->payload['house_photo'] = $this->file['house_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['scholarship_photo']))
                    $this->payload['scholarship_photo'] = $this->file['scholarship_photo']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['active_college']))
                    $this->payload['active_college'] = $this->file['active_college']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['not_civil_servant']))
                    $this->payload['not_civil_servant'] = $this->file['not_civil_servant']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                if (isset($this->file['pddikti']))
                    $this->payload['pddikti'] = $this->file['pddikti']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

                ## Insert applicant data
                $applicantData = Arr::whereNotNull([
                    'tapp_passPhoto' => $this->payload['pass_photo'] ?? null,
                    'tapp_housePhoto' => $this->payload['house_photo'] ?? null,
                    'tapp_scholarship' => $this->payload['scholarship_photo'] ?? null,
                    'tapp_activeCollege' => $this->payload['active_college'] ?? null,
                    'tapp_notCivilServant' => $this->payload['not_civil_servant'] ?? null,
                    'tapp_PDDikti' => $this->payload['pddikti'] ?? null,
                ]);

                if (!$applicant) {
                    if (($insertIdentity && $insertStudent)
                        || ($identity->identity_id && $student->student_id)
                    ) {
                        $applicantData['ta_id'] = $this->auth['account_id'];
                        $applicantData['tid_id'] = $identity->identity_id ?? $insertIdentity->tid_id;
                        $applicantData['ts_id'] = $student->student_id ?? $insertStudent->ts_id;

                        $insertApplicant = ApplicantModel::insert($applicantData);
                    }
                } else {
                    $insertApplicant = ApplicantModel::find($applicant->applicant_id)->update($applicantData);
                }

                if (isset($insertApplicant) && !$insertApplicant) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }

                // Return transaction status
                return (object) [
                    'status' => true,
                ];
            });
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Function to update data from database
     * @return object|bool
     */
    public function propose()
    {
        ## Formatting additional data which not payload
        // ## Get applicant id
        $applicant =
            ApplicantModel::select('tapp_id AS applicant_id')
            ->where('ta_id', $this->auth['account_id'])
            ->first();


        ## Formatting payload
        // Code here...

        try {

            return DB::transaction(function () use ($applicant) {

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'tapp_status' => "PENDING",
                    'tapp_proposeDate' => date('Y-m-d H:i:s')
                ]);

                ## Propose applicantion
                $applicantUpdate = ApplicantModel::find($applicant->applicant_id)->update($dbPayload);

                if (!$applicantUpdate) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }

                // Return transaction status
                return (object) [
                    'status' => true,
                ];
            });
        } catch (Exception $e) {

            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
