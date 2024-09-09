<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '11',
            'name' => 'Test User 1',
            'email' => 'test1@efsme.com',
        ]);
        User::create([
            'id' => '12',
            'name' => 'Test user 2',
            'email' => 'test2@efsme.com',
        ]);
    }
}
