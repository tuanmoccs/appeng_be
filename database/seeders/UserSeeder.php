<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Tạo tài khoản người dùng mẫu
        User::create([
            'name' => 'Người dùng',
            'email' => 'admin215@example.com',
            'password' => Hash::make('password'),
        ]);

    }
}
