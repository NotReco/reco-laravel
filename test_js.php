<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tmdb = app(App\Services\TmdbService::class);
$media = $tmdb->getMedia(1399, 'tv');
$tc = $tmdb->getTrailerCandidates($media['videos'] ?? []);
echo Illuminate\Support\Js::from($tc);
