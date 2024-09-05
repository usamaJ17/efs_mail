<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\GeneralSetting;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Str::matchAll("%http://%", config('app.url'))->count() > 0) {
            $superAdminEmail = 'superadmin@' . Str::remove('http://', config('app.url'));
        } else {
            $superAdminEmail = 'superadmin@' . Str::remove('https://', config('app.url'));
        }

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => $superAdminEmail,
            'password' => 'password@123 ',
            'is_admin' => true,
        ]);

        GeneralSetting::create([
            'key' => 'logo',
            'value' => ''
        ]);
    }
}
