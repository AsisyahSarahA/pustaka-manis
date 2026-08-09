<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Menyalin database ke folder database/backups dan menghapus backup lebih dari 30 hari';

    public function handle(): int
    {
        $driver = config('database.default');
        $backupDir = database_path('backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s');

        if ($driver === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            $dest = $backupDir . DIRECTORY_SEPARATOR . $filename . '.sqlite';

            if (!file_exists($dbPath)) {
                $this->error("Database tidak ditemukan: {$dbPath}");

                return self::FAILURE;
            }

            copy($dbPath, $dest);
            $this->info("Backup SQLite dibuat: {$dest}");
        } elseif ($driver === 'mysql') {
            // Fallback: export via mysqldump jika tersedia, jika tidak copy struktural tidak mungkin
            $dest = $backupDir . DIRECTORY_SEPARATOR . $filename . '.sql';
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $db = config('database.connections.mysql.database');
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');

            $cmd = sprintf(
                '"%s" -h%s -P%s -u%s%s %s > "%s"',
                'mysqldump',
                escapeshellarg($host),
                (int) $port,
                escapeshellarg($user),
                $pass !== '' ? ' -p' . escapeshellarg($pass) : '',
                escapeshellarg($db),
                $dest
            );

            exec($cmd . ' 2>&1', $output, $code);

            if ($code !== 0) {
                // Fallback: simpan snapshot ringan lewat Eloquent? Lebih baik pakai metode SQL standar
                $this->warn('mysqldump tidak ditemukan. Mencoba ekspor via SHOW CREATE...');
                $handle = fopen($dest, 'w');
                foreach (DB::select('SHOW TABLES') as $row) {
                    $table = array_values((array) $row)[0];
                    $create = DB::select("SHOW CREATE TABLE `{$table}`")[0]->{'Create Table'};
                    fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n{$create};\n\n");
                }
                fclose($handle);
                $this->info("Backup struktur MySQL dibuat: {$dest} (tanpa data)");
            } else {
                $this->info("Backup MySQL dibuat: {$dest}");
            }
        } else {
            $this->error("Driver database '{$driver}' tidak didukung untuk backup.");

            return self::FAILURE;
        }

        // Hapus backup lebih dari 30 hari
        $cutoff = Carbon::now()->subDays(30)->timestamp;
        $deleted = 0;

        foreach (glob($backupDir . DIRECTORY_SEPARATOR . 'backup_*') as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Menghapus {$deleted} backup lama (lebih dari 30 hari).");
        }

        return self::SUCCESS;
    }
}