<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo accounts and sample catalog data must never be seeded into production.
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $this->call([
            DemoUsersSeeder::class,
            DemoCatalogSeeder::class,
        ]);
    }
}
