<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $user->update([

            'last_login_at' => now(),

            'last_login_ip' => $request->ip(),

        ]);

        if ($user->hasRole('Administrator')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Maintenance Manager')) {
            return redirect()->route('manager.dashboard');
        }

        if ($user->hasRole('Maintenance Supervisor')) {
            return redirect()->route('supervisor.dashboard');
        }

        if ($user->hasRole('Maintenance Technician')) {
            return redirect()->route('technician.dashboard');
        }

        Auth::logout();

        return redirect()

            ->route('login')

            ->withErrors([

                'email' =>

                'Your account has no assigned role. Please contact the System Administrator.'

            ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

