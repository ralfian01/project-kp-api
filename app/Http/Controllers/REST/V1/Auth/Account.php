<?php

namespace App\Http\Controllers\REST\V1\Auth;

use App\Http\Controllers\REST\BaseREST;
use App\Http\Controllers\REST\Errors;
use App\Models\AccountMetaModel;
use App\Models\AccountModel;
use Firebase\JWT\JWT;

class Account extends BaseREST
{
    public function __construct(
        ?array $payload = [],
        ?array $file = [],
        ?array $auth = []
    ) {
        $this->payload = $payload;
        $this->file = $file;
        $this->auth = $auth;
    }

    /**
     * @var array Property that contains the payload rules
     */
    protected $payloadRules = [];

    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [];


    /**
     * The method that starts the main activity
     * @return null
     */
    protected function mainActivity($id = null)
    {
        return $this->nextValidation();
    }

    /**
     * Handle the next step of payload validation
     * @return void
     */
    private function nextValidation()
    {
        $authorize = $this->authorize();

        if (!$authorize->status) {
            return $this->error(
                (new Errors)
                    ->setStatus($authorize->errorCode, $authorize->errorStatus)
                    ->setMessage($authorize->errorMessage)
            );
        }

        $this->auth = $authorize->auth;


        return $this->get();
    }

    /** 
     * Function to get data 
     * @return object
     */
    public function get()
    {
        $reqTime = time();
        $expTime = $reqTime + (3600 * 24); // 1 Hour * 24: Expires in 24 hours
        $jwtObject = [
            'iss' => 'JWT Authentication',
            'iat' => $reqTime,
            'exp' => $expTime,
            'uid_b64' => base64_encode($this->auth['uuid']),
            'username' => $this->auth['username']
        ];

        $response = [
            'token' => JWT::encode($jwtObject, env('APP_KEY'), 'HS256'),
        ];

        return $this->respond(200, $response);
    }

    /**
     * @return object|bool
     */
    private function authorize()
    {
        // Collect header: Authorization
        $authorizeHead = $this->request->header('Authorization');

        // Make sure authorization type is Basic
        if ($authorizeHead && strpos($authorizeHead, 'Basic ') === 0) {

            // Authorize client using Basic
            $authorizeData = explode(':', base64_decode(str_replace('Basic ', '', $authorizeHead)));

            // Validate username & password from model
            $accData =
                AccountModel::select(
                    'ta_id as account_id',
                    'ta_uuid as uuid',
                    'ta_username as username',
                    'ta_statusDelete as statusDelete',
                    'ta_statusActive as statusActive'
                )
                ->where('ta_username', $authorizeData[0])
                ->where('ta_password', hash('SHA256', $authorizeData[1]))
                ->getWithPrivileges();

            if (!$accData->isEmpty()) {

                $accData = $accData[0];

                // Make sure account not deleted and not suspended
                if (
                    !$accData->statusDelete
                    && $accData->statusActive
                ) {
                    // Remove unnecessary columns
                    unset($accData->statusDelete);
                    unset($accData->statusActive);

                    return (object) [
                        'status' => true,
                        'auth' => $accData
                    ];
                }
            }
        }

        return (object) [
            'status' => false,
            'errorCode' => 401,
            'errorStatus' => "UNAUTHORIZED",
            'errorMessage' => "You do not have permission to access this resource"
        ];
    }
}
