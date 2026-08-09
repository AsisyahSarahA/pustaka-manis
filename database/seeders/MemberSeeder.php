<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Services\CodeGenerator;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    protected const TARGET_SISWA = 40;
    protected const TARGET_GURU = 10;

    /**
     * Seed anggota: 40 siswa + 10 guru (Faker locale id_ID).
     * Idempotent: menambah kekurangannya saja bila sudah ada sebagian data.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $classes = ['VII-A', 'VII-B', 'VII-C', 'VIII-A', 'VIII-B', 'VIII-C', 'IX-A', 'IX-B', 'IX-C'];
        $jabatans = [
            'Kepala Sekolah', 'Waka Kurikulum', 'Waka Kesiswaan',
            'Guru Matematika', 'Guru Bahasa Indonesia', 'Guru Bahasa Inggris',
            'Guru IPA', 'Guru IPS', 'Guru PJOK', 'Guru BK',
        ];

        $toCreateSiswa = max(0, self::TARGET_SISWA - Member::where('type', 'siswa')->count());
        $toCreateGuru = max(0, self::TARGET_GURU - Member::where('type', 'guru')->count());
        $total = $toCreateSiswa + $toCreateGuru;

        if ($total === 0) {
            $this->command?->info('Kuota anggota sudah terpenuhi (' . self::TARGET_SISWA . ' siswa, ' . self::TARGET_GURU . ' guru).');

            return;
        }

        $bar = $this->command?->getOutput()->createProgressBar($total);
        $bar?->start();

        for ($i = 0; $i < $toCreateSiswa; $i++) {
            Member::create([
                'member_code' => CodeGenerator::generateMemberCode('siswa'),
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'type' => 'siswa',
                'identity_number' => $this->uniqueNis($faker),
                'department_class' => $classes[array_rand($classes)],
                'phone' => '08' . $faker->numerify('##########'),
                'is_active' => true,
            ]);
            $bar?->advance();
        }

        for ($i = 0; $i < $toCreateGuru; $i++) {
            Member::create([
                'member_code' => CodeGenerator::generateMemberCode('guru'),
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'type' => 'guru',
                'identity_number' => $this->uniqueNip($faker),
                'department_class' => $jabatans[array_rand($jabatans)],
                'phone' => '08' . $faker->numerify('##########'),
                'is_active' => true,
            ]);
            $bar?->advance();
        }

        $bar?->finish();
        $this->command?->info('');
        $this->command?->info("{$total} anggota baru berhasil ditambahkan.");
    }

    protected function uniqueNis($faker): string
    {
        do {
            $nis = '20' . $faker->numberBetween(21, 25) . $faker->numerify('####');
        } while (Member::where('identity_number', $nis)->exists());

        return $nis;
    }

    protected function uniqueNip($faker): string
    {
        do {
            $nip = $faker->numerify('19##############');
        } while (Member::where('identity_number', $nip)->exists());

        return $nip;
    }
}
