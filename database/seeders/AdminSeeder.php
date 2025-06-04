<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Tạo Super Admin
    Admin::create([
      'name' => 'Super Admin',
      'email' => 'admin@example.com',
      'password' => Hash::make('password'),
      'role' => 'super_admin',
      'is_active' => true,
      'email_verified_at' => now(),
    ]);

    // Tạo Admin thường
    Admin::create([
      'name' => 'Admin User',
      'email' => 'admin2@example.com',
      'password' => Hash::make('password'),
      'role' => 'admin',
      'is_active' => true,
      'email_verified_at' => now(),
    ]);
  }
}
