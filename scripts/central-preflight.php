<?php

declare(strict_types=1);

/**
 * MCI Central Master Admin server preflight.
 *
 * Run from the repository root before merging/deploying the central admin release:
 *   /opt/cpanel/ea-php83/root/usr/bin/php scripts/central-preflight.php
 *
 * This script is read-only except for creating a timestamped SQL backup under
 * ~/backups/mci-central. It never runs migrations or modifies application data.
 */

$repo = dirname(__DIR__);
$home = dirname(dirname(dirname(__DIR__)));
$envFile = $repo.'/.env';
$backupDir = $home.'/backups/mci-central';

$pass = 0;
$fail = 0;

function result(bool $ok, string $label, string $detail = ''): void
{
    global $pass, $fail;
    $ok ? $pass++ : $fail++;
    echo ($ok ? 'PASS' : 'FAIL').'  '.$label.($detail !== '' ? ' — '.$detail : '').PHP_EOL;
}

function envValues(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $values = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (($value[0] ?? '') === '"' && str_ends_with($value, '"')) {
            $value = stripcslashes(substr($value, 1, -1));
        } elseif (($value[0] ?? '') === "'" && str_ends_with($value, "'")) {
            $value = substr($value, 1, -1);
        }
        $values[$key] = $value;
    }
    return $values;
}

function commandExists(string $command): bool
{
    $path = trim((string) shell_exec('command -v '.escapeshellarg($command).' 2>/dev/null'));
    return $path !== '';
}

echo "===== MCI CENTRAL SERVER PREFLIGHT =====".PHP_EOL;

$env = envValues($envFile);
result(is_file($envFile), '.env present');
result(($env['APP_ENV'] ?? '') === 'production', 'APP_ENV=production');
result(strtolower((string)($env['APP_DEBUG'] ?? 'false')) === 'false', 'APP_DEBUG=false');
result(!empty($env['APP_KEY']), 'APP_KEY configured');
result(($env['DB_CONNECTION'] ?? '') === 'mysql', 'DB_CONNECTION=mysql');
result(!empty($env['DB_DATABASE']) && !empty($env['DB_USERNAME']), 'Database credentials configured');
result(!empty($env['MAIL_FROM_ADDRESS']), 'MAIL_FROM_ADDRESS configured');

$branch = trim((string) shell_exec('cd '.escapeshellarg($repo).' && git rev-parse --abbrev-ref HEAD 2>/dev/null'));
$sha = trim((string) shell_exec('cd '.escapeshellarg($repo).' && git rev-parse --short HEAD 2>/dev/null'));
result($branch !== '', 'Git repository detected', $branch !== '' ? $branch.' @ '.$sha : '');

try {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = (int)($env['DB_PORT'] ?? 3306);
    $db = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $password = $env['DB_PASSWORD'] ?? '';
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $pdo->query('SELECT 1')->fetchColumn();
    result(true, 'Database connectivity');
} catch (Throwable $e) {
    result(false, 'Database connectivity', $e->getMessage());
}

$mysqldump = trim((string) shell_exec('command -v mysqldump 2>/dev/null'));
if ($mysqldump === '') {
    foreach (['/usr/bin/mysqldump','/usr/local/bin/mysqldump','/opt/cpanel/ea-mysql80/root/usr/bin/mysqldump'] as $candidate) {
        if (is_executable($candidate)) {
            $mysqldump = $candidate;
            break;
        }
    }
}

if ($mysqldump === '') {
    result(false, 'mysqldump available');
} elseif (empty($env['DB_DATABASE']) || empty($env['DB_USERNAME'])) {
    result(false, 'Database backup', 'DB credentials missing');
} else {
    if (!is_dir($backupDir) && !mkdir($backupDir, 0700, true) && !is_dir($backupDir)) {
        result(false, 'Backup directory writable', $backupDir);
    } else {
        @chmod($backupDir, 0700);
        $timestamp = date('Ymd_His');
        $backupFile = $backupDir.'/mci_before_central_'.$timestamp.'.sql';
        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $port = (string)($env['DB_PORT'] ?? '3306');
        $user = $env['DB_USERNAME'] ?? '';
        $db = $env['DB_DATABASE'] ?? '';
        $password = $env['DB_PASSWORD'] ?? '';

        $command = 'MYSQL_PWD='.escapeshellarg($password).' '.escapeshellarg($mysqldump)
            .' --single-transaction --quick --skip-lock-tables'
            .' -h '.escapeshellarg($host)
            .' -P '.escapeshellarg($port)
            .' -u '.escapeshellarg($user)
            .' '.escapeshellarg($db)
            .' > '.escapeshellarg($backupFile).' 2>&1';

        exec($command, $output, $code);
        $ok = $code === 0 && is_file($backupFile) && filesize($backupFile) > 0;
        if (!$ok && is_file($backupFile) && filesize($backupFile) === 0) {
            @unlink($backupFile);
        }
        result($ok, 'Database backup', $ok ? $backupFile.' ('.number_format((int)filesize($backupFile)).' bytes)' : 'mysqldump failed');
    }
}

$php = '/opt/cpanel/ea-php83/root/usr/bin/php';
if (!is_executable($php)) {
    $php = PHP_BINARY;
}

$commands = [
    'artisan about' => escapeshellarg($php).' '.escapeshellarg($repo.'/artisan').' about --no-ansi',
    'migration status' => escapeshellarg($php).' '.escapeshellarg($repo.'/artisan').' migrate:status --no-ansi',
];
foreach ($commands as $label => $command) {
    exec($command.' >/tmp/mci-central-preflight.log 2>&1', $unused, $code);
    result($code === 0, $label);
}

echo PHP_EOL.'PREFLIGHT RESULT: '.$pass.' PASS, '.$fail.' FAIL'.PHP_EOL;
if ($fail === 0) {
    echo "MCI_CENTRAL_PREFLIGHT=PASS".PHP_EOL;
    echo "SAFE TO PROCEED TO CONTROLLED MERGE/DEPLOY GATE".PHP_EOL;
} else {
    echo "MCI_CENTRAL_PREFLIGHT=FAIL".PHP_EOL;
    echo "DO NOT MERGE/DEPLOY UNTIL FAILURES ARE RESOLVED".PHP_EOL;
}

exit($fail === 0 ? 0 : 1);
