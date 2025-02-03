<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivilegeSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['tp_code' => 'ACCOUNT_MANAGE_VIEW', 'tp_description' => 'View account list'],
            ['tp_code' => 'ACCOUNT_MANAGE_SUSPEND', 'tp_description' => 'Suspend or activate account'],
            ['tp_code' => 'ACCOUNT_MANAGE_PRIVILEGE', 'tp_description' => 'Set account privileges'],
            ['tp_code' => 'ADMIN_MANAGE_VIEW', 'tp_description' => 'View admin list'],
            ['tp_code' => 'ADMIN_MANAGE_SUSPEND', 'tp_description' => 'Suspend or activate admin'],
            ['tp_code' => 'ADMIN_MANAGE_PRIVILEGE', 'tp_description' => 'Set admin privileges'],
            ['tp_code' => 'ADMIN_MANAGE_ADD', 'tp_description' => 'Add or delete admin'],
            ['tp_code' => 'EMPLOYEE_MANAGE_VIEW', 'tp_description' => 'Manager view employee'],
            ['tp_code' => 'EMPLOYEE_MANAGE_ADD', 'tp_description' => 'Manager add or delete employee'],
            ['tp_code' => 'EMPLOYEE_MANAGE_MODIFY', 'tp_description' => 'Manager edit employee'],
            ['tp_code' => 'SCHEDULE_MANAGE_VIEW', 'tp_description' => 'Manager view schedule'],
            ['tp_code' => 'SCHEDULE_MANAGE_ADD', 'tp_description' => 'Manager add or delete schedule'],
            ['tp_code' => 'SCHEDULE_MANAGE_MODIFY', 'tp_description' => 'Manager edit schedule'],
            ['tp_code' => 'COMPLAIN_MANAGE_VIEW', 'tp_description' => 'Manager view complain'],
            ['tp_code' => 'COMPLAIN_MANAGE_ADD', 'tp_description' => 'Manager add or delete complain'],
            ['tp_code' => 'COMPLAIN_MANAGE_MODIFY', 'tp_description' => 'Manager edit complain'],
            ['tp_code' => 'PRODUCT_MANAGE_VIEW', 'tp_description' => 'Manager view product'],
            ['tp_code' => 'PRODUCT_MANAGE_ADD', 'tp_description' => 'Manager add or delete product'],
            ['tp_code' => 'PRODUCT_MANAGE_MODIFY', 'tp_description' => 'Manager edit product'],
            ['tp_code' => 'MACHINE_MANAGE_VIEW', 'tp_description' => 'Manager view machine'],
            ['tp_code' => 'MACHINE_MANAGE_ADD', 'tp_description' => 'Manager add or delete machine'],
            ['tp_code' => 'MACHINE_MANAGE_MODIFY', 'tp_description' => 'Manager edit machine'],
        ];

        DB::table('privilege')->insert($data);
    }
}
