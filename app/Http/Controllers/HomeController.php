<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Send the user to the area their role grants, signing out users without one.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $homeRouteName = $user->homeRouteName();

        if ($homeRouteName === null) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta não possui acesso ao sistema.',
            ]);
        }

        return redirect()->route($homeRouteName);
    }
}
