<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::insert("INSERT INTO `expense_types` (`id`, `name`, `start_date`,`status`) VALUES ('9aabf14b-1859-4282-877a-3e206bfe4af1', 'Conveyance Expense','2023-11-22', 1);");
        DB::insert("INSERT INTO `expense_types` (`id`, `name`, `start_date`,`status`) VALUES ('9aabf173-afa0-45c8-a352-ac3bfe8eefbb', 'DSA Settlement','2023-11-22', 1);");
        DB::insert("INSERT INTO `expense_types` (`id`, `name`, `start_date`,`status`) VALUES ('9aabf1b1-d5b3-49e2-bade-3ce9552cbf52', 'general expense','2023-11-22', 1);");
        DB::insert("INSERT INTO `expense_types` (`id`, `name`, `start_date`,`status`) VALUES ('9aabf1da-2757-4510-aa08-d2a82287047b', 'Internet Expenses','2023-11-22', 1);");
        DB::insert("INSERT INTO `expense_types` (`id`, `name`, `start_date`,`status`) VALUES ('9aabf1f9-ca79-4fe8-8a85-53bde463a750', 'Expense Fuel','2023-11-22', 1);");
        DB::insert("INSERT INTO `expense_types` (`id`, `name`, `start_date`,`status`) VALUES ('9aabf21c-ed47-4f0c-a082-4090853245a3', 'Transfer Claim','2023-11-22', 1);");
    }
}
