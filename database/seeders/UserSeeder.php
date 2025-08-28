<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(
            ['email' => 'admin@billingps.com'],
            ['name'=>'Admin','password'=>Hash::make('password'),'role'=>'admin']
        );
        User::updateOrCreate(
            ['email' => 'operator@billingps.com'],
            ['name'=>'Operator','password'=>Hash::make('password'),'role'=>'operator']
        );
    }
}
