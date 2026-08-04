<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles'
        ]);

        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user()->load([
            'department',
            'position',
            'roles'
        ]);

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([

            'first_name' => 'required',

            'middle_name' => 'nullable',

            'last_name' => 'required',

            'suffix' => 'nullable',

            'email' => 'required|email|unique:users,email,' . $user->id,

            'phone' => 'nullable',

            'avatar' => 'nullable|image|max:2048',

        ]);

        $avatar = $user->avatar;

        if ($request->hasFile('avatar')) {

            $avatar = $request
                ->file('avatar')
                ->store('avatars', 'public');
        }

        $user->update([

            'first_name' => $request->first_name,

            'middle_name' => $request->middle_name,

            'last_name' => $request->last_name,

            'suffix' => $request->suffix,

            'email' => $request->email,

            'phone' => $request->phone,

            'avatar' => $avatar,

        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([

            'current_password' => 'required',

            'password' => 'required|confirmed|min:8',

        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {

            return back()
                ->withErrors([
                    'current_password' => 'Current password is incorrect.'
                ]);
        }

        $user->update([

            'password' => Hash::make($request->password)

        ]);

        return back()
            ->with('success', 'Password updated successfully.');
    }
}
