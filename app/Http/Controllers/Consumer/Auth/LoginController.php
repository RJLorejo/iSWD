<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show consumer login page.
     */
    public function create()
    {
        return view('consumer.auth.login');
    }

    /**
     * Authenticate consumer.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Attempt authentication
        |--------------------------------------------------------------------------
        */

        if (
            !Auth::attempt(
                [
                    'email' => $credentials['email'],
                    'password' => $credentials['password'],
                    'is_active' => true,
                ],
                $request->boolean('remember')
            )
        ) {
            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.',
                ])
                ->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Consumer Role
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user->hasRole('Consumer')) {

            Auth::logout();

            return back()
                ->withErrors([
                    'email' => 'This login is for consumer accounts only.',
                ])
                ->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Update Login Information
        |--------------------------------------------------------------------------
        */

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->intended(route('consumer.dashboard'))
            ->with(
                'success',
                'Welcome back to the iSWD Consumer Portal.'
            );
    }
}
