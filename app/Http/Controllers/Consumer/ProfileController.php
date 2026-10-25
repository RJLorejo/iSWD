<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $consumer = $user->consumer()
            ->with('address')
            ->firstOrFail();

        return view('consumer.profile.show', compact('user', 'consumer'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $consumer = $user->consumer()
            ->with('address')
            ->firstOrFail();

        return view('consumer.profile.edit', compact('user', 'consumer'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $consumer = $user->consumer()->firstOrFail();

        $validated = $request->validate([
            // Personal information
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'sex' => ['required', Rule::in(['Male', 'Female', 'Other'])],

            // Contact information
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('consumers', 'phone')->ignore($consumer->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('consumers', 'email')->ignore($consumer->id),
            ],

            // Service address
            'house_no' => ['nullable', 'string', 'max:100'],
            'street' => ['nullable', 'string', 'max:150'],
            'purok' => ['nullable', 'string', 'max:100'],
            'barangay' => ['required', 'string', 'max:150'],
            'municipality' => ['nullable', 'string', 'max:150'],
            'province' => ['nullable', 'string', 'max:150'],

            // Hidden map coordinates
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // Optional password change
            'current_password' => [
                'nullable',
                'required_with:password,password_confirmation',
                'current_password',
            ],
            'password' => [
                'nullable',
                'required_with:current_password,password_confirmation',
                'confirmed',
                Password::min(8),
            ],
            'password_confirmation' => [
                'nullable',
                'required_with:password',
            ],
        ]);

        DB::transaction(function () use ($user, $consumer, $validated) {
            $personal = [
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'] ?? null,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ];

            // Keep User and Consumer details synchronized.
            $user->forceFill($personal);

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            $consumer->forceFill([
                ...$personal,
                'sex' => $validated['sex'],
            ])->save();

            // Create or update the consumer's service address.
            $consumer->address()->updateOrCreate(
                ['consumer_id' => $consumer->id],
                [
                    'house_no' => $validated['house_no'] ?? null,
                    'street' => $validated['street'] ?? null,
                    'purok' => $validated['purok'] ?? null,
                    'barangay' => $validated['barangay'],
                    'municipality' => $validated['municipality'] ?? null,
                    'province' => $validated['province'] ?? null,
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                ]
            );
        });

        return redirect()
            ->route('consumer.profile.show')
            ->with(
                'success',
                !empty($validated['password'])
                    ? 'Your profile and password have been updated successfully.'
                    : 'Your profile has been updated successfully.'
            );
    }

    /**
     * Keep the existing password endpoint available for other pages.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('consumer.profile.edit')
            ->with(
                'password_success',
                'Your password has been changed successfully.'
            );
    }
}
