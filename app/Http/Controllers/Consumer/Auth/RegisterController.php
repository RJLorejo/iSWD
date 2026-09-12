<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Consumer;
use App\Models\ConsumerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show consumer registration form.
     */
    public function create()
    {
        return view('consumer.auth.register');
    }


    /**
     * Register consumer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Account Information
            |--------------------------------------------------------------------------
            */

            'account_number' => [
                'required',
                'string',
                'max:50',
                'unique:consumers,account_number',
            ],

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

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

            'sex' => [
                'required',
                'in:Male,Female',
            ],

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'house_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'street' => [
                'nullable',
                'string',
                'max:150',
            ],

            'purok' => [
                'nullable',
                'string',
                'max:100',
            ],

            'barangay' => [
                'required',
                'string',
                'max:150',
            ],

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            /*
            |--------------------------------------------------------------------------
            | Agreement
            |--------------------------------------------------------------------------
            */

            'terms' => [
                'accepted',
            ],
        ]);


        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'first_name' => $validated['first_name'],

                'middle_name' =>
                $validated['middle_name'] ?? null,

                'last_name' =>
                $validated['last_name'],

                'suffix' =>
                $validated['suffix'] ?? null,

                'email' =>
                $validated['email'],

                'phone' =>
                $validated['phone'],

                'password' =>
                Hash::make($validated['password']),

                'is_active' => true,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Consumer Role
            |--------------------------------------------------------------------------
            */

            $user->assignRole('Consumer');


            /*
            |--------------------------------------------------------------------------
            | Create Consumer Record
            |--------------------------------------------------------------------------
            */

            $consumer = Consumer::create([

                'account_number' =>
                $validated['account_number'],

                'user_id' =>
                $user->id,

                'first_name' =>
                $validated['first_name'],

                'middle_name' =>
                $validated['middle_name'] ?? null,

                'last_name' =>
                $validated['last_name'],

                'suffix' =>
                $validated['suffix'] ?? null,

                'sex' =>
                $validated['sex'],

                'phone' =>
                $validated['phone'],

                'email' =>
                $validated['email'],

                'is_active' => true,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Address
            |--------------------------------------------------------------------------
            */

            ConsumerAddress::create([

                'consumer_id' =>
                $consumer->id,

                'house_no' =>
                $validated['house_no'] ?? null,

                'street' =>
                $validated['street'] ?? null,

                'purok' =>
                $validated['purok'] ?? null,

                'barangay' =>
                $validated['barangay'],

                'municipality' =>
                'Sagay',

                'province' =>
                'Negros Occidental',
            ]);
        });


        return redirect()
            ->route('consumer.login')
            ->with(
                'success',
                'Consumer account created successfully. You may now sign in.'
            );
    }
}
