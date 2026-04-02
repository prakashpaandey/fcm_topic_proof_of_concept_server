<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin account
        User::firstOrCreate(
            ['username' => 'prakash'],
            [
                'name'            => 'Prakash Pandey',
                'profile_details' => 'System administrator account.',
                'password'        => Hash::make('prakashpandey'),
            ]
        );

        // Seed default interests
        $interests = [
            'Travel', 'Technology', 'Food', 'Books',
            'Music', 'Finance', 'Cricket', 'Gaming',
            'Health', 'Sports',
        ];

        foreach ($interests as $name) {
            Interest::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Admin account and default interests seeded!');
        $this->command->info('Login: username=prakash  password=prakashpandey');
    }
}
