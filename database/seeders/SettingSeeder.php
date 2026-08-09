<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed the application's settings.
     */
    public function run(): void
    {
        $settings = [
            ['app_name', 'PustakaManis', 'string', 'Nama aplikasi'],
            ['school_name', 'SMP Negeri 1', 'string', 'Nama sekolah'],
            ['school_address', 'Jl. Pendidikan No. 1', 'string', 'Alamat sekolah'],
            ['school_logo', '', 'string', 'Path logo sekolah'],
            ['loan_days_siswa', '7', 'integer', 'Durasi pinjam siswa (hari)'],
            ['loan_days_guru', '14', 'integer', 'Durasi pinjam guru (hari)'],
            ['loan_days_staf', '14', 'integer', 'Durasi pinjam staf (hari)'],
            ['max_loan_siswa', '2', 'integer', 'Kuota maks pinjam siswa'],
            ['max_loan_guru', '5', 'integer', 'Kuota maks pinjam guru'],
            ['max_loan_staf', '3', 'integer', 'Kuota maks pinjam staf'],
            ['fine_enabled', 'true', 'boolean', 'Aktifkan fitur denda'],
            ['fine_per_day', '500', 'integer', 'Nominal denda per hari (Rp)'],
            ['fine_max_days', '30', 'integer', 'Maks hari denda dihitung'],
            ['module_visitor_enabled', 'true', 'boolean', 'Modul buku tamu & kiosk'],
            ['module_report_enabled', 'true', 'boolean', 'Modul laporan'],
            ['module_fine_enabled', 'true', 'boolean', 'Modul denda'],
            ['module_member_card_enabled', 'true', 'boolean', 'Modul cetak kartu anggota'],
        ];

        foreach ($settings as [$key, $value, $type, $description]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $type, 'description' => $description]
            );
        }
    }
}
