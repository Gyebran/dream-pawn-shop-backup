<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCatalogSeeder extends Seeder
{
    /**
     * Seed a small demo catalog so the Member flow can be exercised on a fresh database (FR-15).
     *
     * Safe to rerun: categories are matched by name, items by unit_code, pivot rows are insert-or-ignore.
     */
    public function run(): void
    {
        $now = now();

        $categories = [
            'Relics' => 'Artefak yang menyimpan sisa-sisa mimpi.',
            'Timepieces' => 'Jam dan penunjuk waktu yang tidak selalu bergerak maju.',
            'Keys & Locks' => 'Kunci dan gembok untuk pintu yang belum tentu ada.',
            'Music Boxes' => 'Kotak musik dengan lagu yang belum pernah ditulis.',
        ];

        $items = [
            ['unit_code' => 'DPS-001', 'name' => 'The Yesterday Watch', 'description' => 'Jam saku kuningan yang selalu menunjukkan waktu kemarin pukul sekarang.', 'categories' => ['Timepieces', 'Relics']],
            ['unit_code' => 'DPS-002', 'name' => 'The Yesterday Watch', 'description' => 'Unit kedua. Jarum detiknya bergerak berlawanan arah pada tengah malam.', 'categories' => ['Timepieces', 'Relics']],
            ['unit_code' => 'DPS-003', 'name' => 'The Forgotten Door', 'description' => 'Kunci besi berukir untuk pintu yang tidak ada di rumah mana pun.', 'categories' => ['Keys & Locks']],
            ['unit_code' => 'DPS-004', 'name' => 'Lucid Essence', 'description' => 'Botol kaca berisi kabut biru yang bergerak ketika tidak diperhatikan.', 'categories' => ['Relics']],
            ['unit_code' => 'DPS-005', 'name' => 'The Other Side', 'description' => 'Kotak musik kayu eboni yang memainkan nada dari sisi lain cermin.', 'categories' => ['Music Boxes', 'Relics']],
            ['unit_code' => 'DPS-006', 'name' => 'Hollow Lantern', 'description' => 'Lentera tanpa api yang tetap menerangi jalan pulang.', 'categories' => ['Relics']],
        ];

        DB::transaction(function () use ($now, $categories, $items): void {
            foreach ($categories as $name => $description) {
                DB::table('categories')->updateOrInsert(
                    ['name' => $name],
                    ['description' => $description, 'is_archived' => false, 'updated_at' => $now, 'created_at' => $now]
                );
            }

            $categoryIds = DB::table('categories')->whereIn('name', array_keys($categories))->pluck('id', 'name');

            foreach ($items as $item) {
                DB::table('items')->updateOrInsert(
                    ['unit_code' => $item['unit_code']],
                    ['name' => $item['name'], 'description' => $item['description'], 'status' => 'available', 'updated_at' => $now, 'created_at' => $now]
                );

                $itemId = DB::table('items')->where('unit_code', $item['unit_code'])->value('id');

                foreach ($item['categories'] as $categoryName) {
                    DB::table('item_category')->insertOrIgnore([
                        'item_id' => $itemId,
                        'category_id' => $categoryIds[$categoryName],
                    ]);
                }
            }
        });
    }
}
