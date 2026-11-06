<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    private function profileLayout(): string
    {
        $user = Auth::user();

        if ($user->hasRole('Administrator')) {
            return 'admin.layouts.app';
        }

        if ($user->hasRole('Maintenance Manager')) {
            return 'maintenance-manager.layouts.app';
        }

        if ($user->hasRole('Maintenance Technician')) {
            return 'technician.layouts.app';
        }

        if ($user->hasRole('Customer Service')) {
            return 'customer-service.layouts.app';
        }

        return 'layouts.app';
    }

    public function show()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles',
        ]);

        $layout = $this->profileLayout();

        return view('profile.show', compact('user', 'layout'));
    }

    public function edit()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles',
        ]);

        $layout = $this->profileLayout();

        return view('profile.edit', compact('user', 'layout'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | PROFILE VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'digits:11',
                'regex:/^09[0-9]{9}$/',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            /*
             * Password fields are intentionally nullable here.
             * We check them separately so an autofilled current
             * password does not force an employee to change it.
             */

            'current_password' => [
                'nullable',
                'string',
            ],

            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],

            'password_confirmation' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PASSWORD VALIDATION
        |--------------------------------------------------------------------------
        */

        $changePassword = !empty($validated['password']);

        if ($changePassword) {
            // A new password requires the current password.
            if (empty($validated['current_password'])) {
                return back()
                    ->withErrors([
                        'current_password' =>
                            'Please enter your current password to change your password.',
                    ])
                    ->withInput(
                        $request->except([
                            'current_password',
                            'password',
                            'password_confirmation',
                        ])
                    );
            }

            // Verify the employee's existing password.
            if (
                !Hash::check(
                    $validated['current_password'],
                    $user->password
                )
            ) {
                return back()
                    ->withErrors([
                        'current_password' =>
                            'Your current password is incorrect.',
                    ])
                    ->withInput(
                        $request->except([
                            'current_password',
                            'password',
                            'password_confirmation',
                        ])
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE PROFILE INFORMATION
        |--------------------------------------------------------------------------
        */

        $firstName = trim($validated['first_name']);
        $middleName = !empty($validated['middle_name'])
            ? trim($validated['middle_name'])
            : null;

        $lastName = trim($validated['last_name']);
        $suffix = !empty($validated['suffix'])
            ? trim($validated['suffix'])
            : null;

        $email = strtolower(trim($validated['email']));

        $phone = !empty($validated['phone'])
            ? trim($validated['phone'])
            : null;

        /*
        |--------------------------------------------------------------------------
        | AVATAR UPLOAD
        |--------------------------------------------------------------------------
        */

        $oldAvatar = $user->avatar;
        $newAvatar = null;

        if ($request->hasFile('avatar')) {
            $newAvatar = $request
                ->file('avatar')
                ->store('avatars', 'public');

            if (!$newAvatar) {
                return back()
                    ->withErrors([
                        'avatar' =>
                            'Unable to upload your profile picture. Please try again.',
                    ])
                    ->withInput(
                        $request->except([
                            'current_password',
                            'password',
                            'password_confirmation',
                        ])
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE PROFILE INFORMATION AND PASSWORD
        |--------------------------------------------------------------------------
        */

        try {
            DB::transaction(function () use (
                $user,
                $firstName,
                $middleName,
                $lastName,
                $suffix,
                $email,
                $phone,
                $newAvatar,
                $changePassword,
                $validated
            ) {
                $user->update([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'suffix' => $suffix,
                    'email' => $email,
                    'phone' => $phone,
                    'avatar' => $newAvatar ?? $user->avatar,
                ]);

                // Change password only when a new password was entered.
                if ($changePassword) {
                    $user->update([
                        'password' => Hash::make(
                            $validated['password']
                        ),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            // Remove the newly uploaded file if saving fails.
            if (
                $newAvatar &&
                Storage::disk('public')->exists($newAvatar)
            ) {
                Storage::disk('public')->delete($newAvatar);
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE OLD AVATAR AFTER SUCCESSFUL UPDATE
        |--------------------------------------------------------------------------
        */

        if (
            $newAvatar &&
            $oldAvatar &&
            $oldAvatar !== $newAvatar &&
            Storage::disk('public')->exists($oldAvatar)
        ) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | SEPARATE PASSWORD UPDATE
    |--------------------------------------------------------------------------
    */

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                'string',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Password updated successfully.');
    }
}
