<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('db:seed');
    echo "Seed Success";
} catch (\Exception $e) {
    file_put_contents('error_full.txt', $e->getMessage() . "\n\n" . $e->getTraceAsString());
    echo "Saved to error_full.txt";
}
