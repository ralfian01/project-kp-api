<?php

namespace App\Http\Controllers\REST\V1\Applicant\General;

use App\Http\Libraries\BaseDBRepo;
use App\Models\ApplicantModel;
use App\Models\ApplicantRevisionModel;
use App\Models\BankModel;
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
    public static function checkApplicantById($applicantId)
    {
        return ApplicantModel::find($applicantId) != null;
    }

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
     * Function to check application status is DRAFT
     * @return bool
     */
    public static function isApplicationStatusDraft($applicantId)
    {
        $data = ApplicantModel::where('tapp_id', $applicantId)->get();

        if ($data->isEmpty()) return true;

        $data = $data->whereIn('tapp_status', ["DRAFT", "REVISE"]);

        return !$data->isEmpty();
    }

    /**
     * Function to check bank availability
     * @return bool
     */
    public static function checkBankAvailability($bankCode)
    {
        $data = BankModel::where('tb_code', $bankCode)->get();

        return !$data->isEmpty();
    }


    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */


    /**
     * Function to get data from database
     * @return array|null|object
     */
    public function getData()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        try {

            $data =
                ApplicantModel::with([
                    'account' => function ($query) {
                        return $query->select(
                            'ta_id',
                            'ta_uuid as uuid',
                            'ta_username as username'
                        );
                    },
                    'identity' => function ($query) {
                        return $query->select(
                            'tid_id',
                            'tid_fullName as full_name',
                            'tid_phoneNumber as phone_number',
                            'tid_idCardNumber as id_card_number',
                            'tid_idCardPhoto as id_card_photo',
                            'tid_idCardProvince as id_card_province',
                            'tid_idCardCity as id_card_city',
                            'tid_idCardDistrict as id_card_district',
                            'tid_idCardVillage as id_card_village',
                            'tid_idCardPostalCode as id_card_postal_code',
                            'tid_idCardAddress as id_card_address',
                            'tid_famCardNumber as family_card_number',
                            'tid_famCardPhoto as family_card_photo',
                            'tid_domicileProvince as domicile_province',
                            'tid_domicileCity as domicile_city',
                            'tid_domicileDistrict as domicile_district',
                            'tid_domicileVillage as domicile_village',
                            'tid_domicilePostalCode as domicile_postal_code',
                            'tid_domicileAddress as domicile_address',
                            'tid_coorLatitude as coor_lat',
                            'tid_coorLongitude as coor_long',
                            'tid_bankNumber as bank_number',
                            'tid_bankName as bank_name',
                            'tid_bankBookPhoto as bank_book_photo',
                        );
                    },
                    'student' => function ($query) {
                        return $query
                            ->with(['majorLevel' => function ($subQuery) {
                                return $subQuery->select(
                                    'tsml_id',
                                    'tsml_id as id',
                                    'tsml_code as code',
                                    'tsml_name as name'
                                );
                            }])
                            ->select(
                                'ts_id',
                                'tsml_id',
                                'ts_studentNumber as student_number',
                                'ts_studentCardPhoto as student_card_photo',
                                'ts_university as university',
                                'ts_majorName as major_name',
                                'ts_scienceType as science_type',
                                'ts_GPA as gpa',
                                'ts_studyPlanCard as study_plan_card',
                                'ts_studyResultCard as study_result_card',
                            );
                    },
                    'revision' => function ($query) {
                        return $query->select(
                            'tapp_id',
                            'tappr_createdAt as revised_date',
                            'tappr_column as column',
                            'tappr_reason as reason',
                            'tappr_revisedBy as revised_by'
                        );
                    }
                ])
                ->addSelect(['ta_id', 'tid_id', 'ts_id', 'tapp_id'])
                ->addSelect([
                    'tapp_recommenderName as recommender_name',
                    'tapp_id as applicant_id',
                    'tapp_passPhoto as pass_photo',
                    'tapp_housePhoto as house_photo',
                    'tapp_scholarship as scholarship',
                    'tapp_activeCollege as active_college',
                    'tapp_notCivilServant as not_civil_servant',
                    'tapp_PDDikti as pddikti',
                    'tapp_status as status',
                    'tapp_proposeDate as propose_date',
                ])
                ->where('ta_id', $this->auth['account_id']);

            // ## Get data by id
            if (isset($this->payload['id'])) {
                $data = $data->where('tapp_id', $this->payload['id']);
            } else {
                // ## Filter data
            }

            $data = $data
                ->get()
                ->map(function ($item) {

                    $item->makeHidden(['ta_id', 'ts_id', 'tid_id', 'tapp_id']);
                    $item->account->makeHidden(['ta_id']);
                    $item->identity->makeHidden(['tid_id']);
                    $item->student->makeHidden(['ts_id']);

                    return $item;
                });

            return (object) [
                'status' => !$data->isEmpty(),
                'data' => $data->isEmpty()
                    ? null
                    : (isset($this->payload['id'])
                        ? $data->toArray()[0]
                        : $data->toArray())
            ];
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
    public function insertData()
    {
        ## Formatting additional data which not payload
        // Code here...


        ## Formatting payload
        // ## Formatting domicile address
        if (isset($this->payload['id_card_as_domicile']) && $this->payload['id_card_as_domicile']) {
            $this->payload['domicile_province'] = $this->payload['id_card_province'] ?? null;
            $this->payload['domicile_city'] = $this->payload['id_card_city'] ?? null;
            $this->payload['domicile_district'] = $this->payload['id_card_district'] ?? null;
            $this->payload['domicile_village'] = $this->payload['id_card_village'] ?? null;
            $this->payload['domicile_postal_code'] = $this->payload['id_card_postal_code'] ?? null;
            $this->payload['domicile_address'] = $this->payload['id_card_address'] ?? null;
        }


        try {

            return DB::transaction(function () {

                // ## Save file
                if (isset($this->file['id_card_photo']))
                    $this->payload['id_card_photo'] = $this->file['id_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");
                // $this->payload['id_card_photo'] = $this->file['id_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['family_card_photo']))
                    $this->payload['family_card_photo'] = $this->file['family_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['bank_book_photo']))
                    $this->payload['bank_book_photo'] = $this->file['bank_book_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                // If id found and Delete keys that have a null value
                $dbPayload = Arr::whereNotNull([
                    'ta_id' => $this->auth['account_id'],
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
                $insertIdentity = IdentityModel::create($dbPayload);

                if (!$insertIdentity) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }


                ## Insert student data
                if (isset($this->file['student_card_photo']))
                    $this->payload['student_card_photo'] = $this->file['student_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['study_plan_card']))
                    $this->payload['study_plan_card'] = $this->file['study_plan_card']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['study_result_card']))
                    $this->payload['study_result_card'] = $this->file['study_result_card']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                $studentData = Arr::whereNotNull([
                    'ta_id' => $this->auth['account_id'],
                    'ts_studentNumber' => $this->payload['student_number'] ?? null,
                    'ts_studentCardPhoto' => $this->payload['student_card_photo'] ?? null,
                    'ts_university' => $this->payload['university'] ?? null,
                    'tsml_id' => $this->payload['major_level'] ?? null,
                    'ts_majorName' => $this->payload['major_name'] ?? null,
                    'ts_scienceType' => $this->payload['science_type'] ?? null,
                    'ts_GPA' => $this->payload['gpa'] ?? null,
                    'ts_studyPlanCard' => $this->payload['study_plan_card'] ?? null,
                    'ts_studyResultCard' => $this->payload['study_result_card'] ?? null,
                ]);

                $insertStudent = StudentModel::create($studentData);

                if (!$insertStudent) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }


                ## Insert applicant data
                if (isset($this->file['pass_photo']))
                    $this->payload['pass_photo'] = $this->file['pass_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['house_photo']))
                    $this->payload['house_photo'] = $this->file['house_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['scholarship_photo']))
                    $this->payload['scholarship_photo'] = $this->file['scholarship_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['active_college']))
                    $this->payload['active_college'] = $this->file['active_college']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['not_civil_servant']))
                    $this->payload['not_civil_servant'] = $this->file['not_civil_servant']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['pddikti']))
                    $this->payload['pddikti'] = $this->file['pddikti']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                $applicantData = Arr::whereNotNull([
                    'tapp_recommenderName' => $this->payload['recommender_name'] ?? null,
                    'tapp_passPhoto' => $this->payload['pass_photo'] ?? null,
                    'tapp_housePhoto' => $this->payload['house_photo'] ?? null,
                    'tapp_scholarship' => $this->payload['scholarship_photo'] ?? null,
                    'tapp_activeCollege' => $this->payload['active_college'] ?? null,
                    'tapp_notCivilServant' => $this->payload['not_civil_servant'] ?? null,
                    'tapp_PDDikti' => $this->payload['pddikti'] ?? null,
                ]);

                $applicantData['ta_id'] = $this->auth['account_id'];
                $applicantData['tid_id'] = $insertIdentity->tid_id;
                $applicantData['ts_id'] = $insertStudent->ts_id;

                $insertApplicant = ApplicantModel::insert($applicantData);

                if (!$insertApplicant) {
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
                'message' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            ];
        }
    }

    /**
     * Function to update data from database
     * @return object|bool
     */
    public function updateData()
    {
        ## Formatting additional data which not payload
        // ## Get applicant id
        $applicant =
            ApplicantModel::select([
                'ts_id as student_id',
                'tid_id as identity_id',
                'tapp_id AS applicant_id'
            ])
            ->where('tapp_id', $this->payload['id'])
            ->first();

        ## Formatting payload
        // ## Formatting domicile address
        if (isset($this->payload['id_card_as_domicile']) && $this->payload['id_card_as_domicile']) {
            $this->payload['domicile_province'] = $this->payload['id_card_province'] ?? null;
            $this->payload['domicile_city'] = $this->payload['id_card_city'] ?? null;
            $this->payload['domicile_district'] = $this->payload['id_card_district'] ?? null;
            $this->payload['domicile_village'] = $this->payload['id_card_village'] ?? null;
            $this->payload['domicile_postal_code'] = $this->payload['id_card_postal_code'] ?? null;
            $this->payload['domicile_address'] = $this->payload['id_card_address'] ?? null;
        }

        // // ## Get domicile region name
        // if (isset($this->payload['domicile_province'])) {
        //     $this->payload['domicile_province'] = ProvinceModel::find($this->payload['domicile_province'])->tipr_name;
        // }
        // if (isset($this->payload['domicile_city'])) {
        //     $this->payload['domicile_city'] = CityModel::find($this->payload['domicile_city'])->tict_name;
        // }
        // if (isset($this->payload['domicile_district'])) {
        //     $this->payload['domicile_district'] = DistrictModel::find($this->payload['domicile_district'])->tidt_name;
        // }
        // if (isset($this->payload['domicile_village'])) {
        //     $this->payload['domicile_village'] = VillageModel::find($this->payload['domicile_village'])->tivl_name;
        // }


        // // ## Get id card region name
        // if (isset($this->payload['id_card_province'])) {
        //     $this->payload['id_card_province'] = ProvinceModel::find($this->payload['id_card_province'])->tipr_name;
        // }
        // if (isset($this->payload['id_card_city'])) {
        //     $this->payload['id_card_city'] = CityModel::find($this->payload['id_card_city'])->tict_name;
        // }
        // if (isset($this->payload['id_card_district'])) {
        //     $this->payload['id_card_district'] = DistrictModel::find($this->payload['id_card_district'])->tidt_name;
        // }
        // if (isset($this->payload['id_card_village'])) {
        //     $this->payload['id_card_village'] = VillageModel::find($this->payload['id_card_village'])->tivl_name;
        // }


        // // ## Get name of major level
        // if (isset($this->payload['major_level'])) {
        //     $this->payload['major_level'] = StudentMajorLevelModel::find($this->payload['major_level'])->tsml_name;
        // }


        try {

            return DB::transaction(function () use ($applicant) {

                // ## Save file
                if (isset($this->file['id_card_photo']))
                    $this->payload['id_card_photo'] = $this->file['id_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['family_card_photo']))
                    $this->payload['family_card_photo'] = $this->file['family_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['bank_book_photo']))
                    $this->payload['bank_book_photo'] = $this->file['bank_book_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

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

                ## Update identity data
                $updateIdentity = IdentityModel::find($applicant->identity_id)->update($dbPayload);

                if (!$updateIdentity) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }


                if (isset($this->file['student_card_photo']))
                    $this->payload['student_card_photo'] = $this->file['student_card_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['study_plan_card']))
                    $this->payload['study_plan_card'] = $this->file['study_plan_card']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['study_result_card']))
                    $this->payload['study_result_card'] = $this->file['study_result_card']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                ## Insert student data
                $studentData = Arr::whereNotNull([
                    'ts_studentNumber' => $this->payload['student_number'] ?? null,
                    'ts_studentCardPhoto' => $this->payload['student_card_photo'] ?? null,
                    'ts_university' => $this->payload['university'] ?? null,
                    'tsml_id' => $this->payload['major_level'] ?? null,
                    'ts_majorName' => $this->payload['major_name'] ?? null,
                    'ts_scienceType' => $this->payload['science_type'] ?? null,
                    'ts_GPA' => $this->payload['gpa'] ?? null,
                    'ts_studyPlanCard' => $this->payload['study_plan_card'] ?? null,
                    'ts_studyResultCard' => $this->payload['study_result_card'] ?? null,
                ]);

                $updateStudent = StudentModel::find($applicant->student_id)->update($studentData);

                if (!$updateStudent) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }


                if (isset($this->file['pass_photo']))
                    $this->payload['pass_photo'] = $this->file['pass_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['house_photo']))
                    $this->payload['house_photo'] = $this->file['house_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['scholarship_photo']))
                    $this->payload['scholarship_photo'] = $this->file['scholarship_photo']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['active_college']))
                    $this->payload['active_college'] = $this->file['active_college']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['not_civil_servant']))
                    $this->payload['not_civil_servant'] = $this->file['not_civil_servant']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                if (isset($this->file['pddikti']))
                    $this->payload['pddikti'] = $this->file['pddikti']->move(storage_path('images'), date('YmdHis') . "_" . rand(100000, 999999) . ".webp");

                ## Insert applicant data
                $applicantData = Arr::whereNotNull([
                    'tapp_recommenderName' => $this->payload['recommender_name'] ?? null,
                    'tapp_passPhoto' => $this->payload['pass_photo'] ?? null,
                    'tapp_housePhoto' => $this->payload['house_photo'] ?? null,
                    'tapp_scholarship' => $this->payload['scholarship_photo'] ?? null,
                    'tapp_activeCollege' => $this->payload['active_college'] ?? null,
                    'tapp_notCivilServant' => $this->payload['not_civil_servant'] ?? null,
                    'tapp_PDDikti' => $this->payload['pddikti'] ?? null,
                ]);

                $updateApplicant = ApplicantModel::find($applicant->applicant_id)->update($applicantData);

                if (!$updateApplicant) {
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
                'message' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
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
            ApplicantModel::select([
                'ts_id as student_id',
                'tid_id as identity_id',
                'tapp_id AS applicant_id'
            ])
            ->where('tapp_id', $this->payload['id'])
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
                $applicantUpdate = ApplicantModel::find($this->payload['id'])->update($dbPayload);

                if (!$applicantUpdate) {
                    $tableName = IdentityModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }

                ## Delete application revision            
                ApplicantRevisionModel::where('tapp_id', $applicant->applicant_id)->delete();

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
