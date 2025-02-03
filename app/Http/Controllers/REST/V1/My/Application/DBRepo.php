<?php

namespace App\Http\Controllers\REST\V1\My\Application;

use App\Http\Libraries\BaseDBRepo;
use App\Models\ApplicantModel;
use App\Models\ApplicantRejectionModel;
use App\Models\BankModel;
use App\Models\IdentityModel;
use App\Models\IndonesianRegion\CityModel;
use App\Models\IndonesianRegion\DistrictModel;
use App\Models\IndonesianRegion\ProvinceModel;
use App\Models\IndonesianRegion\VillageModel;
use App\Models\StudentMajorLevelModel;
use App\Models\StudentModel;
use App\Models\ValidRegionModel;
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
     * Function to check application status is DRAFT
     * @return bool
     */
    public static function isApplicationStatusDraft($account_id)
    {
        $data = ApplicantModel::where('ta_id', $account_id)->get();

        if ($data->isEmpty()) return true;

        $data = $data->whereIn('tapp_status', ["DRAFT", "REJECT"]);

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

    /**
     * Function to check region id
     * @return object ['province_id', 'city_id']
     */
    public static function checkRegionId($province_id = null, $city_id = null, $district_id = null, $village_id = null)
    {
        $statuses = (object) [
            'province' => null,
            'city' => null,
            'district' => null,
            'village' => null,
        ];

        if ($province_id) {
            $statuses->province =
                !ProvinceModel::where('tipr_id', (int) $province_id)
                    ->get()
                    ->isEmpty();
        }

        if ($city_id) {
            $statuses->city =
                !CityModel::where('tict_id', (int) $city_id)
                    ->get()
                    ->isEmpty();
        }

        if ($district_id) {
            $statuses->district =
                !DistrictModel::where('tidt_id', (int) $district_id)
                    ->get()
                    ->isEmpty();
        }

        if ($village_id) {
            $statuses->village =
                !VillageModel::where('tivl_id', (int) $village_id)
                    ->get()
                    ->isEmpty();
        }

        return $statuses;
    }

    /**
     * Function to check major level id
     * @return bool
     */
    public static function checkMajorLevelId($major_level_id)
    {
        return
            !StudentMajorLevelModel::where('tsml_id', (int) $major_level_id)
                ->get()
                ->isEmpty();
    }

    /**
     * Function to check valid region
     * @return bool
     */
    public static function checkValidRegion($province_id, $city_id)
    {
        if (ValidRegionModel::where('tipr_id', $province_id)->get()->isEmpty())
            return false;

        if (ValidRegionModel::where('tict_id', $city_id)->get()->isEmpty())
            return false;

        return true;
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
                        $query
                            ->with(
                                [
                                    'provinceIdCard' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tipr_id',
                                            'tipr_name as province_name'
                                        );
                                    },
                                    'cityIdCard' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tict_id',
                                            'tict_name as city_name'
                                        );
                                    },
                                    'districtIdCard' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tidt_id',
                                            'tidt_name as district_name'
                                        );
                                    },
                                    'villageIdCard' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tivl_id',
                                            'tivl_name as village_name'
                                        );
                                    },
                                    'provinceDomicile' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tipr_id',
                                            'tipr_name as province_name'
                                        );
                                    },
                                    'cityDomicile' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tict_id',
                                            'tict_name as city_name'
                                        );
                                    },
                                    'districtDomicile' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tidt_id',
                                            'tidt_name as district_name'
                                        );
                                    },
                                    'villageDomicile' => function ($subQuery) {
                                        return $subQuery->select(
                                            'tivl_id',
                                            'tivl_name as village_name'
                                        );
                                    }
                                ]
                            )
                            ->select(
                                'tid_id',
                                'tid_idCardProvince',
                                'tid_idCardCity',
                                'tid_idCardDistrict',
                                'tid_idCardVillage',
                                'tid_domicileProvince',
                                'tid_domicileCity',
                                'tid_domicileDistrict',
                                'tid_domicileVillage',
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
                    'rejection' => function ($query) {
                        return $query->select(
                            'tapp_id',
                            'tappr_createdAt as rejected_date',
                            'tappr_column as column',
                            'tappr_reason as reason',
                            'tappr_rejectedBy as rejected_by'
                        );
                    }
                ])
                ->addSelect(['ta_id', 'tid_id', 'ts_id', 'tapp_id'])
                ->addSelect([
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
                ->where('ta_id', $this->auth['account_id'])
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
                'data' => $data->isEmpty() ? null : $data->first()->toArray()
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
                    'tsml_id' => $this->payload['major_level'] ?? null,
                    'ts_majorName' => $this->payload['major_name'] ?? null,
                    'ts_scienceType' => $this->payload['science_type'] ?? null,
                    'ts_GPA' => $this->payload['gpa'] ?? null,
                    'ts_studyPlanCard' => $this->payload['study_plan_card'] ?? null,
                    'ts_studyResultCard' => $this->payload['study_result_card'] ?? null,
                ]);

                if (!$student) {
                    $studentData['ta_id'] = $this->auth['account_id'];

                    $insertStudent = StudentModel::create($studentData);
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

                if (isset($this->file['scholarship']))
                    $this->payload['scholarship'] = $this->file['scholarship']->storeAs('images', date('YmdHis') . "_" . rand(111111, 999999) . ".webp");

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
                    'tapp_scholarship' => $this->payload['scholarship'] ?? null,
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
                    $applicantData['ta_id'] = $this->auth['account_id'];
                    $applicantData['tid_id'] = $identity->identity_id;
                    $applicantData['ts_id'] = $student->student_id;

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

                ## Propose application
                $applicantUpdate = ApplicantModel::find($applicant->applicant_id)->update($dbPayload);

                if (!$applicantUpdate) {
                    $tableName = ApplicantModel::tableName();
                    throw new Exception("Failed when insert data into table \"{$tableName}\"");
                }

                ## Delete application rejection
                ApplicantRejectionModel::where('tapp_id', $applicant->applicant_id)->delete();


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
}
