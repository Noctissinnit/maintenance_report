<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Line;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lines = Line::all()->keyBy('name');

        $machines = [
            [
                'name' => 'Mesin Potong Otomatis Line A',
                'code' => 'MCH-001',
                'line_id' => $lines['Line A']->id,
                'description' => 'Mesin potong baja dengan presisi tinggi untuk produksi massal',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Stamping Hidrolik',
                'code' => 'MCH-002',
                'line_id' => $lines['Line A']->id,
                'description' => 'Mesin press hidrolik 500 ton untuk stamping part',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Pengelasan Robot',
                'code' => 'MCH-003',
                'line_id' => $lines['Line B']->id,
                'description' => 'Robot welding 6 axis untuk perakitan otomatis',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Bubut CNC',
                'code' => 'MCH-004',
                'line_id' => $lines['Line B']->id,
                'description' => 'Mesin bubut CNC untuk finishing presisi',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Grinder Silinder',
                'code' => 'MCH-005',
                'line_id' => $lines['Line C']->id,
                'description' => 'Mesin penghalus untuk diameter silinder',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Finishing Otomatis',
                'code' => 'MCH-006',
                'line_id' => $lines['Line C']->id,
                'description' => 'Finishing dan polishing otomatis dengan belt sanding',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Pembanding Dimensi',
                'code' => 'MCH-007',
                'line_id' => $lines['Inspection']->id,
                'description' => 'CMM (Coordinate Measuring Machine) untuk QC',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Kompresor Udara',
                'code' => 'MCH-008',
                'line_id' => $lines['Utility']->id,
                'description' => 'Kompresor 50 HP untuk supply tekanan udara',
                'status' => 'active',
            ],
            [
                'name' => 'Mesin Potong Laser CNC',
                'code' => 'MCH-009',
                'line_id' => $lines['Line D']->id,
                'description' => 'Laser cutting 3000W untuk material thin sheet',
                'status' => 'inactive',
            ],
            [
                'name' => 'Mesin Bending Press',
                'code' => 'MCH-010',
                'line_id' => $lines['Line A']->id,
                'description' => 'Press bending untuk material stainless steel',
                'status' => 'active',
            ],
        ];

        foreach ($machines as $machine) {
            Machine::create($machine);
        }
    }
}
