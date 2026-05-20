<?php
/**
 * Artisan Runner — Jalankan perintah Artisan via browser
 * PENTING: Hapus file ini setelah selesai migrasi!
 *
 * Akses via: https://domainanda.com/artisan_runner.php?secret=GANTI_INI&cmd=migrate
 */

define('SECRET_KEY', 'GANTI_DENGAN_STRING_ACAK_PANJANG'); // Ganti sebelum upload!
define('LARAVEL_ROOT', dirname(__DIR__) . '/meditrack');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET_KEY) {
    http_response_code(403);
    die('Forbidden.');
}

$allowed_commands = [
    'migrate'              => ['migrate', '--force'],
    'migrate:fresh'        => ['migrate:fresh', '--force'],
    'key:generate'         => ['key:generate', '--force'],
    'optimize'             => ['optimize'],
    'optimize:clear'       => ['optimize:clear'],
    'storage:link'         => ['storage:link'],
    'config:cache'         => ['config:cache'],
    'route:cache'          => ['route:cache'],
    'view:cache'           => ['view:cache'],
];

$cmd = $_GET['cmd'] ?? '';

if (!array_key_exists($cmd, $allowed_commands)) {
    $list = implode(', ', array_keys($allowed_commands));
    die("Perintah tidak dikenal. Yang tersedia: {$list}");
}

chdir(LARAVEL_ROOT);

require LARAVEL_ROOT . '/vendor/autoload.php';

$app = require_once LARAVEL_ROOT . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo '<pre>';
$kernel->call($allowed_commands[$cmd][0], array_slice($allowed_commands[$cmd], 1));
echo htmlspecialchars($kernel->output());
echo '</pre>';
echo '<p style="color:green">Selesai. Jangan lupa hapus file ini!</p>';
