<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$accounts = [
    [
        'name' => 'Thông Nguyễn',
        'email' => 'thongnguyen.111004@gmail.com',
        'password' => Hash::make('123456'),
        'role' => UserRole::ADMIN,
    ],
    [
        'name' => 'Thanh Nguyễn',
        'email' => 'holorblack@gmail.com',
        'password' => Hash::make('123456'),
        'role' => UserRole::MODERATOR,
    ],
    [
        'name' => 'Tester Trần',
        'email' => 'recothelizard369@gmail.com',
        'password' => Hash::make('123456'),
        'role' => UserRole::TESTER,
    ],
    [
        'name' => 'Thông Đức',
        'email' => 'thong.nd.64cntt@ntu.edu.vn',
        'password' => Hash::make('123456'),
        'role' => UserRole::USER,
    ],
];

echo "Updating default accounts...\n";
foreach ($accounts as $account) {
    User::updateOrCreate(
        ['email' => $account['email']],
        $account
    );
}

echo "Testing accounts...\n";
foreach ($accounts as $account) {
    $user = User::where('email', $account['email'])->first();
    echo "- Email: " . $user->email . "\n";
    echo "  Name: " . $user->name . "\n";
    echo "  Role: " . $user->role->value . "\n";
    
    $loginSuccess = Auth::attempt(['email' => $user->email, 'password' => '123456']);
    echo "  Login test (123456): " . ($loginSuccess ? "SUCCESS" : "FAILED") . "\n";
}

echo "\nTotal Users: " . User::count() . "\n";
