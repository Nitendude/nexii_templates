<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--prune : Delete old hourly backups after creating a new one}';

    protected $description = 'Create a compressed database backup in storage/app/backups/database.';

    public function handle(): int
    {
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (($connection['driver'] ?? null) !== 'mysql') {
            $this->error('Database backups currently support the mysql connection only.');
            return self::FAILURE;
        }

        $database = (string) ($connection['database'] ?? '');
        if ($database === '') {
            $this->error('No database name is configured.');
            return self::FAILURE;
        }

        $backupDirectory = storage_path('app/backups/database');
        $temporaryDirectory = storage_path('app/backups/.tmp');
        File::ensureDirectoryExists($backupDirectory, 0750, true);
        File::ensureDirectoryExists($temporaryDirectory, 0750, true);

        $timestamp = now()->format('Ymd_His');
        $backupPath = "{$backupDirectory}/{$database}_{$timestamp}.sql.gz";
        $defaultsFile = "{$temporaryDirectory}/mysqldump_{$timestamp}_" . bin2hex(random_bytes(4)) . '.cnf';

        File::put($defaultsFile, $this->mysqlDefaultsFile($connection));
        @chmod($defaultsFile, 0600);

        $gzip = gzopen($backupPath, 'wb9');
        if ($gzip === false) {
            File::delete($defaultsFile);
            $this->error('Unable to open backup file for writing.');
            return self::FAILURE;
        }

        $dumpBinary = $this->dumpBinary();

        try {
            try {
                if ($dumpBinary) {
                    $this->dumpWithBinary($dumpBinary, $defaultsFile, $database, $gzip);
                } else {
                    $this->dumpWithPdo($connectionName, $database, $gzip);
                }
            } finally {
                gzclose($gzip);
                File::delete($defaultsFile);
            }
        } catch (\Throwable $exception) {
            File::delete($backupPath);
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        if ($this->option('prune')) {
            $this->pruneOldBackups($backupDirectory);
        }

        $this->info("Database backup created: {$backupPath}");
        return self::SUCCESS;
    }

    private function dumpBinary(): ?string
    {
        $finder = new ExecutableFinder();

        return $finder->find('mysqldump') ?: $finder->find('mariadb-dump');
    }

    /**
     * @param resource $gzip
     */
    private function dumpWithBinary(string $binary, string $defaultsFile, string $database, $gzip): void
    {
        $process = new Process([
            $binary,
            "--defaults-extra-file={$defaultsFile}",
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--events',
            '--default-character-set=utf8mb4',
            $database,
        ]);
        $process->setTimeout(900);

        $process->run(function (string $type, string $buffer) use ($gzip): void {
            if ($type === Process::OUT) {
                gzwrite($gzip, $buffer);
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()) ?: 'Database backup failed.');
        }
    }

    /**
     * @param resource $gzip
     */
    private function dumpWithPdo(string $connectionName, string $database, $gzip): void
    {
        $connection = DB::connection($connectionName);
        $pdo = $connection->getPdo();

        gzwrite($gzip, "-- APM database backup\n");
        gzwrite($gzip, "-- Database: {$database}\n");
        gzwrite($gzip, '-- Created: ' . now()->toDateTimeString() . "\n\n");
        gzwrite($gzip, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);

        foreach ($tables as $tableRow) {
            $table = (string) ($tableRow[0] ?? '');
            if ($table === '') {
                continue;
            }

            $quotedTable = $this->quoteIdentifier($table);
            $createRow = $pdo->query("SHOW CREATE TABLE {$quotedTable}")->fetch(PDO::FETCH_ASSOC);
            $createSql = (string) ($createRow['Create Table'] ?? array_values($createRow ?: [])[1] ?? '');

            gzwrite($gzip, "DROP TABLE IF EXISTS {$quotedTable};\n");
            gzwrite($gzip, "{$createSql};\n\n");

            $statement = $pdo->query("SELECT * FROM {$quotedTable}");
            $statement->setFetchMode(PDO::FETCH_ASSOC);

            while ($row = $statement->fetch()) {
                $columns = array_map(fn ($column) => $this->quoteIdentifier((string) $column), array_keys($row));
                $values = array_map(fn ($value) => $value === null ? 'NULL' : $pdo->quote((string) $value), array_values($row));
                gzwrite($gzip, "INSERT INTO {$quotedTable} (" . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n");
            }

            gzwrite($gzip, "\n");
        }

        gzwrite($gzip, "SET FOREIGN_KEY_CHECKS=1;\n");
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function mysqlDefaultsFile(array $connection): string
    {
        $lines = [
            '[client]',
            'user="' . $this->escapeOptionFileValue((string) ($connection['username'] ?? '')) . '"',
            'password="' . $this->escapeOptionFileValue((string) ($connection['password'] ?? '')) . '"',
        ];

        $socket = trim((string) ($connection['unix_socket'] ?? ''));
        if ($socket !== '') {
            $lines[] = 'socket="' . $this->escapeOptionFileValue($socket) . '"';
        } else {
            $lines[] = 'host="' . $this->escapeOptionFileValue((string) ($connection['host'] ?? '127.0.0.1')) . '"';
            $lines[] = 'port="' . $this->escapeOptionFileValue((string) ($connection['port'] ?? '3306')) . '"';
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function escapeOptionFileValue(string $value): string
    {
        return str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
    }

    private function pruneOldBackups(string $backupDirectory): void
    {
        $retentionHours = max(1, (int) env('DB_BACKUP_RETENTION_HOURS', 168));
        $cutoff = now()->subHours($retentionHours)->getTimestamp();

        foreach (File::glob("{$backupDirectory}/*.sql.gz") ?: [] as $file) {
            if (File::lastModified($file) < $cutoff) {
                File::delete($file);
            }
        }
    }
}
