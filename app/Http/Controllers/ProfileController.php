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

        if ($user->hasRole('Consumer')) {
            return 'consumer.layouts.app';
        }

        return 'layouts.app';
    }

    public function show()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles',
            'consumer.address',
        ]);

        $layout = $this->profileLayout();

        return view(
            'profile.show',
            compact(
                'user',
                'layout'
            )
        );
    }

    public function edit()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles',
            'consumer.address',
        ]);

        $layout = $this->profileLayout();

        return view(
            'profile.edit',
            compact(
                'user',
                'layout'
            )
        );
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
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
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $validated['first_name'] = trim(
            $validated['first_name']
        );

        $validated['middle_name'] = !empty(
            $validated['middle_name']
        )
            ? trim($validated['middle_name'])
            : null;

        $validated['last_name'] = trim(
            $validated['last_name']
        );

        $validated['suffix'] = !empty(
            $validated['suffix']
        )
            ? trim($validated['suffix'])
            : null;

        $validated['email'] = strtolower(
            trim($validated['email'])
        );

        $validated['phone'] = !empty(
            $validated['phone']
        )
            ? trim($validated['phone'])
            : null;

        $oldAvatar = $user->avatar;
        $newAvatar = null;

        if ($request->hasFile('avatar')) {
            $newAvatar = $request
                ->file('avatar')
                ->store(
                    'avatars',
                    'public'
                );

            $validated['avatar'] = $newAvatar;
        }

        try {
            DB::transaction(function () use (
                $user,
                $validated
            ) {
                $user->update($validated);

                $consumer = $user->consumer;

                if ($consumer) {
                    $consumer->update([
                        'first_name' =>
                            $validated['first_name'],

                        'middle_name' =>
                            $validated['middle_name'],

                        'last_name' =>
                            $validated['last_name'],

                        'suffix' =>
                            $validated['suffix'],

                        'email' =>
                            $validated['email'],

                        'phone' =>
                            $validated['phone'],
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            if (
                $newAvatar &&
                Storage::disk('public')->exists($newAvatar)
            ) {
                Storage::disk('public')
                    ->delete($newAvatar);
            }

            throw $exception;
        }

        if (
            $newAvatar &&
            $oldAvatar &&
            $oldAvatar !== $newAvatar &&
            Storage::disk('public')->exists($oldAvatar)
        ) {
            Storage::disk('public')
                ->delete($oldAvatar);
        }

        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                'Profile updated successfully.'
            );
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                'string',
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

        if (
            !Hash::check(
                $validated['current_password'],
                $user->password
            )
        ) {
            return back()
                ->withErrors([
                    'current_password' =>
                        'Current password is incorrect.',
                ]);
        }

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                'Password updated successfully.'
            );
    }
}
