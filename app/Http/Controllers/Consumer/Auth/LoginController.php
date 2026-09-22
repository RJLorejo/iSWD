<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function create()
    {
        return view('consumer.auth.login');
    }

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

        $user = User::query()
            ->with('consumer')
            ->where('email', $credentials['email'])
            ->first();

        if (
            !$user ||
            !Hash::check(
                $credentials['password'],
                $user->password
            )
        ) {
            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.',
                ])
                ->onlyInput('email');
        }

        if (!$user->hasRole('Consumer')) {
            return back()
                ->withErrors([
                    'email' => 'This login is for consumer accounts only.',
                ])
                ->onlyInput('email');
        }

        $consumer = $user->consumer;

        if (!$consumer) {
            return back()
                ->withErrors([
                    'email' => 'No consumer record is associated with this account. Please contact Sagay Water District.',
                ])
                ->onlyInput('email');
        }

        if (
            $consumer->verification_status === 'Pending Verification' ||
            $consumer->verification_status === 'Rejected'
        ) {
            Auth::login(
                $user,
                $request->boolean('remember')
            );

            $request->session()->regenerate();

            return redirect()
                ->route('consumer.registration.status');
        }

        if ($consumer->verification_status !== 'Verified') {
            return back()
                ->withErrors([
                    'email' => 'Your consumer account has not yet been verified by Sagay Water District.',
                ])
                ->onlyInput('email');
        }

        if (
            !$consumer->is_active ||
            !$user->is_active
        ) {
            return back()
                ->withErrors([
                    'email' => 'Your consumer account is currently inactive. Please contact Sagay Water District for assistance.',
                ])
                ->onlyInput('email');
        }

        Auth::login(
            $user,
            $request->boolean('remember')
        );

        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect()
            ->intended(
                route('consumer.dashboard')
            )
            ->with(
                'success',
                'Welcome back to the iSWD Consumer Portal.'
            );
    }
}
