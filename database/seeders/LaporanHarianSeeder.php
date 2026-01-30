<?php

namespace Database\Seeders;

use App\Models\LaporanHarian;
use App\Models\User;
use App\Models\Machine;
use App\Models\SparePart;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LaporanHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'operator');
        })->get();

        if ($users->isEmpty()) {
            return;
        }

        $machines = Machine::all();
        $spareParts = SparePart::all();

        if ($machines->isEmpty() || $spareParts->isEmpty()) {
            return;
        }

        $catatan = [
            'Bearing rusak dan perlu diganti', 
            'Sabuk putus karena overload', 
            'Motor mati tidak berputar', 
            'Sensor error pada limit switch', 
            'Hidrolik leak pada piston rod',
            'Gir pecah pada transmission',
            'Connector longgar menyebabkan konsleting',
            'Overheating pada bearing spindle',
            'Vibration tinggi pada drive shaft',
            'Power loss akibat kabel putus',
            'Maintenance rutin',
            'Pemeriksaan berkala sesuai jadwal',
            'Penggantian oli hydraulic',
            'Cleaning dan greasing',
            null
        ];

        $jenisPekerjaan = ['corrective', 'preventive', 'modifikasi', 'utility'];

        // Create 60 sample laporan
        for ($i = 0; $i < 60; $i++) {
            $user = $users->random();
            $machine = $machines->random();
            $sparePart = $spareParts->random();
            $jenis = $jenisPekerjaan[array_rand($jenisPekerjaan)];
            
            // Calculate downtime based on jenis_pekerjaan
            if ($jenis === 'corrective') {
                $startTime = Carbon::now()->subDays(rand(0, 30))->setTime(rand(6, 14), rand(0, 59));
                $endTime = $startTime->copy()->addMinutes(rand(10, 480)); // 10 minutes to 8 hours
                $downtime = $startTime->diffInMinutes($endTime);
            } else {
                $startTime = null;
                $endTime = null;
                $downtime = 0;
            }
            
            $betweenFailure = rand(100, 2000);

            LaporanHarian::create([
                'user_id' => $user->id,
                'machine_id' => $machine->id,
                'mesin_name' => $machine->name,
                'line' => $machine->line->name,
                'spare_part_id' => $sparePart->id,
                'catatan' => $catatan[array_rand($catatan)],
                'sparepart' => $sparePart->name,
                'qty_sparepart' => rand(1, 5),
                'jenis_pekerjaan' => $jenis,
                'scope' => ['Electrik', 'Mekanik', 'Utility', 'Building'][array_rand(['Electrik', 'Mekanik', 'Utility', 'Building'])],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'downtime_min' => $downtime,
                'tipe_laporan' => ['harian', 'mingguan', 'bulanan'][array_rand(['harian', 'mingguan', 'bulanan'])],
                'tanggal_laporan' => Carbon::now()->subDays(rand(0, 30))->toDateString(),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('60 sample laporan harian dengan machine dan sparepart created successfully!');
    }
}
