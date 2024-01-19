<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfa84-aced-4e3c-89ee-78137cb433e9', 'DSA Advance',1);");
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfaa9-71e1-429e-8284-436b14f66194', 'Salary Advance', 1);");
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfad0-53a2-48c4-b0cb-9a97915b56b7', 'General Imprest Advance', 1);");
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfaea-3362-4952-8de9-7ad0f8208d48', 'Electricity Imprest Advance', 1);");
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfb02-268c-4984-b3cd-0c9bf9caeede', 'Advance To Staff', 1);");
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfb1d-bbea-4b68-8338-e4e85d48733a', 'SIFA Loan', 1);");
        DB::insert("INSERT INTO `advance_types` (`id`, `name`,`status`) VALUES ('9aabfb35-3df1-4a97-aa31-fa72c7bb8e1c', 'Device EMI', 1);");
    }
}
