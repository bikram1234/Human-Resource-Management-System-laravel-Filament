<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class MasEmployeesTableSeeder extends Seeder
{
    public function run()
    {
       // Create a user
       $user = FilamentUser::create([
           'first_name' => 'code',
           'last_name' => 'bird',
           'email' => 'code.bird@example.com',
           'password' => Hash::make('password'),
           // Add other fields as needed...
           'emp_id' => '9936',
        //    'grade_id' => 'G001',
        //    'grade_step_id' => 'GS001',
        //    'department_id' => 1,
        //    'section_id' => 1,
        //    'designation_id' => 'D001',
           'date_of_appointment' => '2024-01-19',
           'gender' => 'Male',
           'employment_type' => 'Regular',
          // 'region_id' => 1,
       ]);
   
               // Get the 'super-admin' role
         $role = Role::where('name', 'super-admin')->first();

         // Check if the role exists
         if ($role) {
            // Assign the role to the user
            $user->assignRole($role);
         } else {
            // Handle the case where the role does not exist
            // You may create the role here or throw an exception
         }
    }
}
