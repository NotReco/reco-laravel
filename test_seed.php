<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'Movies: ' . App\Models\Movie::count() . PHP_EOL;
echo 'Users: ' . App\Models\User::count() . PHP_EOL;

// Try seeding step by step
$users = App\Models\User::pluck('id')->toArray();
$movies = App\Models\Movie::pluck('id')->toArray();

echo 'User IDs count: ' . count($users) . PHP_EOL;
echo 'Movie IDs count: ' . count($movies) . PHP_EOL;

// Simulate watchlist seeding
try {
    $movieCount = rand(5, 15);
    $minCount = min($movieCount, count($movies));
    echo "Trying to pick $minCount movies from " . count($movies) . " available" . PHP_EOL;
    
    if (count($movies) < 2) {
        echo "ERROR: Not enough movies in DB! Need at least 2 movies." . PHP_EOL;
    } else {
        $selectedMovies = (array) array_rand(array_flip($movies), $minCount);
        echo "Selected " . count($selectedMovies) . " movies OK" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "WATCHLIST ERROR: " . $e->getMessage() . PHP_EOL;
}
