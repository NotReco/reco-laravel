<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index(Request $request)
    {
        // Re-use the existing profile.edit view from Breeze
        // but it could be expanded later to include app-specific preferences (theme, privacy)
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
}
