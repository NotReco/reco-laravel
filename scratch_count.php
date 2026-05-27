<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Movies: " . \App\Models\Movie::count() . "\n";
echo "TV Shows: " . \App\Models\TvShow::count() . "\n";
echo "Reviews: " . \App\Models\Review::count() . "\n";
echo "Comments: " . \App\Models\Comment::count() . "\n";
echo "Watchlists: " . \Illuminate\Support\Facades\DB::table('watchlists')->count() . "\n";
