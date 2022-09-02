<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'user1',
        //     'email' => 'user1@user.com',
        // ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'user2',
        //     'email' => 'user2@user.com',
        // ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'user3',
        //     'email' => 'user3@user.com',
        // ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'user4',
        //     'email' => 'user4@user.com',
        // ]);

        \Orchid\Support\Facades\Dashboard::modelClass(\Orchid\Platform\Models\User::class)
            ->createAdmin('admin', 'admin@admin.com', 'password90.');
    }
}
