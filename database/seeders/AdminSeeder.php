<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@holomia.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@holomia.com',
                'password' => bcrypt('Admin@123456'),
                'role' => 'super_admin',
            ]);
        }
    }
}