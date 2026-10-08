<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower(trim($request->email));

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        // Staff only. Consumers use their separate OTP system.
        if (
            $user &&
            !$user->hasAnyRole([
                'Administrator',
                'Customer Service',
                'Maintenance Manager',
                'Maintenance Technician',
            ])
        ) {
            $user = null;
        }

        if ($user && $user->is_active) {
            Password::broker()->sendResetLink([
                'email' => $user->email,
            ]);
        }

        // Same message for existing and unknown accounts.
        return back()->with(
            'status',
            'If an eligible employee account exists, a password reset link will be sent to its email address.'
        );
    }
}
