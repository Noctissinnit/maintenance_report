<?php

namespace Database\Seeders;

use App\Models\SparePart;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spareParts = [
            [
                'name' => 'Bearing SKF 6205',
                'code' => 'SP-001',
                'category' => 'Bearing',
                'description' => 'Bearing bola SKF seri 6205 untuk spindle mesin',
                'status' => 'active',
            ],
            [
                'name' => 'V-Belt Taper Lock',
                'code' => 'SP-002',
                'category' => 'Transmission',
                'description' => 'V-belt ukuran A untuk pulley drive',
                'status' => 'active',
            ],
            [
                'name' => 'Hydraulic Oil Shell Tellus',
                'code' => 'SP-003',
                'category' => 'Fluid',
                'description' => 'Hydraulic oil Shell Tellus S4 VX 68 untuk press',
                'status' => 'active',
            ],
            [
                'name' => 'Packing Oil Seal',
                'code' => 'SP-004',
                'category' => 'Seal',
                'description' => 'Oil seal double lip untuk shaft seal',
                'status' => 'active',
            ],
            [
                'name' => 'Coupling Flexible',
                'code' => 'SP-005',
                'category' => 'Transmission',
                'description' => 'Flexible coupling untuk drive transmission',
                'status' => 'active',
            ],
            [
                'name' => 'Motor Brake 24VDC',
                'code' => 'SP-006',
                'category' => 'Electrical',
                'description' => 'Electromagnetic brake untuk emergency stop',
                'status' => 'active',
            ],
            [
                'name' => 'Relay Omron 24VDC',
                'code' => 'SP-007',
                'category' => 'Electrical',
                'description' => 'Control relay untuk mesin otomatis',
                'status' => 'active',
            ],
            [
                'name' => 'Proximity Sensor Inductive',
                'code' => 'SP-008',
                'category' => 'Sensor',
                'description' => 'Inductive proximity switch M18 DC 3 wire',
                'status' => 'active',
            ],
            [
                'name' => 'Welding Electrode',
                'code' => 'SP-009',
                'category' => 'Consumable',
                'description' => 'Electrode las E7018 untuk mesin las',
                'status' => 'active',
            ],
            [
                'name' => 'Cutting Tool Carbide Insert',
                'code' => 'SP-010',
                'category' => 'Tool',
                'description' => 'Carbide insert untuk turning tool holder',
                'status' => 'active',
            ],
            [
                'name' => 'Drive Belt Timing Pulley',
                'code' => 'SP-011',
                'category' => 'Transmission',
                'description' => 'Timing belt dan pulley HTD 5mm pitch',
                'status' => 'active',
            ],
            [
                'name' => 'Hydraulic Hose Braided',
                'code' => 'SP-012',
                'category' => 'Fluid',
                'description' => 'Selang hydraulic SAE 100R13 untuk high pressure',
                'status' => 'active',
            ],
            [
                'name' => 'Pressure Valve Manifold',
                'code' => 'SP-013',
                'category' => 'Hydraulic',
                'description' => 'Pressure relief valve untuk sistem hydraulic',
                'status' => 'active',
            ],
            [
                'name' => 'PLC Programmable Logic Controller',
                'code' => 'SP-014',
                'category' => 'Electrical',
                'description' => 'PLC Mitsubishi FX3U untuk automation control',
                'status' => 'inactive',
            ],
            [
                'name' => 'Ball Joint Linkage',
                'code' => 'SP-015',
                'category' => 'Mechanical',
                'description' => 'Ball joint untuk mekanisme linkage',
                'status' => 'active',
            ],
        ];

        foreach ($spareParts as $sparePart) {
            SparePart::create($sparePart);
        }
    }
}
