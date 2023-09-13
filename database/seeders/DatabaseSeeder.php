<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    { 
        // AFTER ENTERING THE CODES IN THE FACTORIES AND SEEDERS, COME TO THIS FILE AND 
        // ADD THE CODES BELOW IN A SPECIFIC ORDER.
        \App\Models\User::factory(1000)->create();

        $this->call(EventSeeder::class);

        $this->call(AttendeeSeeder::class);



        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
