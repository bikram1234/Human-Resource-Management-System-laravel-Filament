<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanAdvanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::insert("INSERT INTO `loan_advancetypes` (`id`, `name`,`status`,'condition') VALUES ('9b3d113c-8545-4c7f-a003-b782ce2945e5', 'Advance Loan min',1, '(amount => 5000)');");
        DB::insert("INSERT INTO `loan_advancetypes` (`id`, `name`,`status`,'condition') VALUES ('9b3d154a-ca9c-48b3-bc48-06398ca01472', 'Advance Loan Max', 1,'(5000 < amount <= 100000 )');");
        DB::insert("INSERT INTO `loan_advancetypes` (`id`, `name`,`status`,'condition') VALUES ('9b551902-3954-40f0-aed0-4bcde658e269', 'Advance Settlement', 1,'Advance Settlement');");
    }
}
