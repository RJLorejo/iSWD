<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Determine the correct layout based on the authenticated user's role.
     */
    private function profileLayout(): string
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Administrator
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Your actual Spatie role is "Administrator", not "Admin".
        |
        */

        if ($user->hasRole('Administrator')) {
            return 'admin.layouts.app';
        }


        /*
        |--------------------------------------------------------------------------
        | Maintenance Manager
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Maintenance Manager')) {
            return 'maintenance-manager.layouts.app';
        }


        /*
        |--------------------------------------------------------------------------
        | Maintenance Technician
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Maintenance Technician')) {
            return 'technician.layouts.app';
        }


        /*
        |--------------------------------------------------------------------------
        | Customer Service
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Customer Service')) {
            return 'customer-service.layouts.app';
        }


        /*
        |--------------------------------------------------------------------------
        | Consumer
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            return 'consumer.layouts.app';
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        */

        return 'layouts.app';
    }


    /**
     * Display authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles',
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


    /**
     * Display edit profile page.
     */
    public function edit()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles',
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


    /**
     * Update authenticated user's profile.
     */
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

                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
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


        /*
        |--------------------------------------------------------------------------
        | Avatar
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('avatar')) {

            /*
             * Delete old avatar.
             */

            if ($user->avatar) {

                Storage::disk('public')
                    ->delete(
                        $user->avatar
                    );
            }


            /*
             * Store new avatar.
             */

            $validated['avatar'] = $request
                ->file('avatar')
                ->store(
                    'avatars',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */

        $user->update(
            $validated
        );


        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                'Profile updated successfully.'
            );
    }


    /**
     * Update authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([

            'current_password' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
            ],
        ]);


        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Verify Current Password
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);


        return back()
            ->with(
                'success',
                'Password updated successfully.'
            );
    }
}
