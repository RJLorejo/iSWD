
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.reset-password', [
            'request' => $request,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $email = Str::lower(trim($request->email));

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (
            !$user ||
            !$user->is_active ||
            !$user->hasAnyRole([
                'Administrator',
                'Customer Service',
                'Maintenance Manager',
                'Maintenance Technician',
            ])
        ) {
            return back()->withErrors([
                'email' => 'This password reset link is invalid or has expired.',
            ]);
        }

        $status = Password::broker()->reset(
            [
                'email' => $user->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Your password has been updated. You can now sign in.');
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}
