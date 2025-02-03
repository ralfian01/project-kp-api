<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed role
        $roleData = [
            ['tr_code' => 'ROOT_ADMIN', 'tr_name' => 'Root Admin'],
            ['tr_code' => 'ADMIN', 'tr_name' => 'Admin'],
            ['tr_code' => 'CS', 'tr_name' => 'Customer Service'],
            ['tr_code' => 'LEADER', 'tr_name' => 'Leader']
        ];

        DB::table('role')->insert($roleData);

        // Seed role privilege
        $rolePrivilegeData = [
            // ROOT_ADMIN
            ['tr_id' => 1, 'tp_id' => 1],
            ['tr_id' => 1, 'tp_id' => 2],
            ['tr_id' => 1, 'tp_id' => 3],
            ['tr_id' => 1, 'tp_id' => 4],
            ['tr_id' => 1, 'tp_id' => 5],
            ['tr_id' => 1, 'tp_id' => 6],
            ['tr_id' => 1, 'tp_id' => 7],
            ['tr_id' => 1, 'tp_id' => 8],
            ['tr_id' => 1, 'tp_id' => 9],
            ['tr_id' => 1, 'tp_id' => 10],
            ['tr_id' => 1, 'tp_id' => 11],
            ['tr_id' => 1, 'tp_id' => 12],
            ['tr_id' => 1, 'tp_id' => 13],
            ['tr_id' => 1, 'tp_id' => 14],
            ['tr_id' => 1, 'tp_id' => 15],
            ['tr_id' => 1, 'tp_id' => 16],
            ['tr_id' => 1, 'tp_id' => 17],
            ['tr_id' => 1, 'tp_id' => 18],
            ['tr_id' => 1, 'tp_id' => 19],
            ['tr_id' => 1, 'tp_id' => 20],
            ['tr_id' => 1, 'tp_id' => 21],
            ['tr_id' => 1, 'tp_id' => 22],

            // ADMIN
            ['tr_id' => 2, 'tp_id' => 1],
            ['tr_id' => 2, 'tp_id' => 2],
            ['tr_id' => 2, 'tp_id' => 3],
            ['tr_id' => 2, 'tp_id' => 8],
            ['tr_id' => 2, 'tp_id' => 9],
            ['tr_id' => 2, 'tp_id' => 10],
            ['tr_id' => 2, 'tp_id' => 11],
            ['tr_id' => 2, 'tp_id' => 12],
            ['tr_id' => 2, 'tp_id' => 13],
            ['tr_id' => 2, 'tp_id' => 14],
            ['tr_id' => 2, 'tp_id' => 15],
            ['tr_id' => 2, 'tp_id' => 16],
            ['tr_id' => 2, 'tp_id' => 17],
            ['tr_id' => 2, 'tp_id' => 18],
            ['tr_id' => 2, 'tp_id' => 19],
            ['tr_id' => 2, 'tp_id' => 20],
            ['tr_id' => 2, 'tp_id' => 21],
            ['tr_id' => 2, 'tp_id' => 22],

            // CS
            ['tr_id' => 3, 'tp_id' => 14],
            ['tr_id' => 3, 'tp_id' => 15],
            ['tr_id' => 3, 'tp_id' => 16],

            // LEADER
            ['tr_id' => 4, 'tp_id' => 8],
            ['tr_id' => 4, 'tp_id' => 9],
            ['tr_id' => 4, 'tp_id' => 10],
            ['tr_id' => 4, 'tp_id' => 11],
            ['tr_id' => 4, 'tp_id' => 12],
            ['tr_id' => 4, 'tp_id' => 13],
            ['tr_id' => 4, 'tp_id' => 14],
        ];

        DB::table('role__privilege')->insert($rolePrivilegeData);
    }
}
