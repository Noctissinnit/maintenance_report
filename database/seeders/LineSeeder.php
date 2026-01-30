<?php

namespace Database\Seeders;

use App\Models\Line;
use Illuminate\Database\Seeder;

class LineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lines = [
            [
                'name' => 'Line A',
                'code' => 'LN-A',
                'description' => 'Lini produksi A - Pemotongan dan Stamping',
                'status' => 'active',
            ],
            [
                'name' => 'Line B',
                'code' => 'LN-B',
                'description' => 'Lini produksi B - Pengelasan dan Pembubutan',
                'status' => 'active',
            ],
            [
                'name' => 'Line C',
                'code' => 'LN-C',
                'description' => 'Lini produksi C - Grinding dan Finishing',
                'status' => 'active',
            ],
            [
                'name' => 'Line D',
                'code' => 'LN-D',
                'description' => 'Lini produksi D - Laser Cutting (Tambahan)',
                'status' => 'inactive',
            ],
            [
                'name' => 'Inspection',
                'code' => 'LN-INS',
                'description' => 'Lini Quality Control dan Pemeriksaan',
                'status' => 'active',
            ],
            [
                'name' => 'Utility',
                'code' => 'LN-UT',
                'description' => 'Area Utility - Kompresor dan Support',
                'status' => 'active',
            ],
        ];

        foreach ($lines as $line) {
            Line::create($line);
        }
    }
}
