<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::insert("INSERT INTO `roles` (`id`, `name`, `guard_name`) VALUES ('1', 'super-admin','filament');");
        DB::insert("INSERT INTO `roles` (`id`, `name`, `guard_name`) VALUES ('2', 'Employee','filament');");
        DB::insert("INSERT INTO `roles` (`id`, `name`, `guard_name`) VALUES ('3', 'Manager','filament');");
        DB::insert("INSERT INTO `roles` (`id`, `name`, `guard_name`) VALUES ('4', 'Section Head','filament');");
        DB::insert("INSERT INTO `roles` (`id`, `name`, `guard_name`) VALUES ('5', 'Department Head','filament');");
    
    }
}
