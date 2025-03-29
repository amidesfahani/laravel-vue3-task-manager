<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Amid Esfahani',
        //     'email' => 'amid@laravel.com',
        //     'password' => bcrypt('password'),
        // ]);

        $user = User::firstOrCreate([
            'email' => 'amid@laravel.com',
        ],[
            'name' => 'Amid Esfahani',
            'password' => bcrypt('password'),
        ]);

        Task::factory(10)->create([
            'user_id' => $user->id,
        ]);
    }
}
