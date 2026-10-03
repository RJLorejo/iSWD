<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerService\StoreConsumerRequest;
use App\Http\Requests\CustomerService\UpdateConsumerRequest;
use App\Models\Consumer;
use App\Models\ConsumerAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConsumerController extends Controller
{
    /**
     * Display consumers.
     */
    public function index(Request $request)
    {
        $query = Consumer::query()
            ->with([
                'address',
                'user',
            ]);


        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {

                $q->where(
                    'account_number',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'middle_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'address',
                        function ($address) use ($search) {

                            $address
                                ->where(
                                    'house_no',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'street',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'purok',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'barangay',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'municipality',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'province',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            });
        }


        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status
            );
        }


        $consumers = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalConsumers = Consumer::count();

        $activeConsumers = Consumer::where(
            'is_active',
            true
        )->count();

        $inactiveConsumers = Consumer::where(
            'is_active',
            false
        )->count();

        $todayConsumers = Consumer::whereDate(
            'created_at',
            today()
        )->count();

        return view(
            'customer-service.consumers.index',
            compact(
                'consumers',
                'totalConsumers',
                'activeConsumers',
                'inactiveConsumers',
                'todayConsumers'
            )
        );
    }

    /**
     * Print filtered consumer report.
     */
    public function printReport(Request $request)
    {
        $query = Consumer::query()
            ->with([
                'address',
                'user',
            ]);


        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'account_number',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'middle_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'address',
                        function ($address) use ($search) {

                            $address
                                ->where(
                                    'house_no',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'street',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'purok',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'barangay',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'municipality',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'province',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status
            );
        }

        $consumers = $query
            ->latest('created_at')
            ->get();

        return view(
            'customer-service.consumers.print-report',
            compact('consumers')
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        return view(
            'customer-service.consumers.create'
        );
    }


    /**
     * Store consumer.
     */
    public function store(
        StoreConsumerRequest $request
    ) {
        $validated =
            $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Generate Temporary Password
        |--------------------------------------------------------------------------
        */

        $temporaryPassword =
            'SWD@' . Str::upper(
                Str::random(8)
            );


        $consumer = DB::transaction(
            function () use (
                $validated,
                $temporaryPassword
            ) {

                /*
                |--------------------------------------------------------------------------
                | Create Portal User
                |--------------------------------------------------------------------------
                */

                $user = User::create([

                    'employee_id' => null,

                    'first_name' =>
                    $validated['first_name'],

                    'middle_name' =>
                    $validated['middle_name']
                        ?? null,

                    'last_name' =>
                    $validated['last_name'],

                    'suffix' =>
                    $validated['suffix']
                        ?? null,

                    'phone' =>
                    $validated['phone'],

                    'email' =>
                    $validated['email'],

                    'password' =>
                    Hash::make(
                        $temporaryPassword
                    ),

                    'is_active' => true,
                ]);


                $user->assignRole(
                    'Consumer'
                );


                /*
                |--------------------------------------------------------------------------
                | Create Consumer
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
                    $validated['middle_name']
                        ?? null,

                    'last_name' =>
                    $validated['last_name'],

                    'suffix' =>
                    $validated['suffix']
                        ?? null,

                    'sex' =>
                    $validated['sex'],

                    'phone' =>
                    $validated['phone'],

                    'email' =>
                    $validated['email'],

                    /*
                    |--------------------------------------------------------------------------
                    | CS-created consumers are already verified
                    |--------------------------------------------------------------------------
                    */

                    'verification_status' =>
                    'Verified',

                    'verified_at' =>
                    now(),

                    'verified_by' =>
                    auth()->id(),

                    'verification_reason' =>
                    null,

                    'registration_source' =>
                    'Customer Service',

                    'is_active' =>
                    true,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Registered Service Address
                |--------------------------------------------------------------------------
                */

                ConsumerAddress::create([

                    'consumer_id' =>
                    $consumer->id,

                    'house_no' =>
                    $validated['house_no']
                        ?? null,

                    'street' =>
                    $validated['street']
                        ?? null,

                    'purok' =>
                    $validated['purok']
                        ?? null,

                    'barangay' =>
                    $validated['barangay'],

                    'municipality' =>
                    $validated['municipality'],

                    'province' =>
                    $validated['province'],

                    /*
                    |--------------------------------------------------------------------------
                    | Registered Service Coordinates
                    |--------------------------------------------------------------------------
                    */

                    'latitude' =>
                    $validated['latitude'],

                    'longitude' =>
                    $validated['longitude'],
                ]);


                return $consumer;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Redirect With One-Time Credentials
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'customer-service.consumers.show',
                $consumer
            )
            ->with(
                'account_created',
                true
            )
            ->with(
                'temporary_password',
                $temporaryPassword
            )
            ->with(
                'success',
                'Consumer account created successfully.'
            );
    }


    /**
     * Display consumer details.
     */
    public function show(
        Consumer $consumer
    ) {
        $consumer->load([
            'address',
            'user',
            'verifier',
            'complaints.division',
            'complaints.category',
        ]);


        return view(
            'customer-service.consumers.show',
            compact('consumer')
        );
    }


    /**
     * Show edit form.
     */
    /**
     * Show edit form.
     */
    public function edit(
        Consumer $consumer
    ) {
        /*
    |--------------------------------------------------------------------------
    | Protect Self-Registered Consumers
    |--------------------------------------------------------------------------
    |
    | Customer Service may view self-registered consumers while their
    | registration is being reviewed, but only the Administrator handles
    | verification.
    |
    | Once the Administrator verifies the registration, Customer Service
    | may edit the consumer normally.
    |
    | Consumers created directly by Customer Service are already verified,
    | so this restriction does not affect them.
    |
    */

        if (
            $consumer->registration_source === 'Self Registration' &&
            $consumer->verification_status !== 'Verified'
        ) {
            return redirect()
                ->route(
                    'customer-service.consumers.show',
                    $consumer
                )
                ->with(
                    'error',
                    $consumer->verification_status === 'Rejected'
                        ? 'This self-registration was rejected. The consumer must correct and resubmit the registration before it can be edited by Customer Service.'
                        : 'This self-registered consumer is awaiting administrator verification and cannot be edited yet.'
                );
        }

        $consumer->load([
            'address',
            'user',
        ]);


        return view(
            'customer-service.consumers.edit',
            compact('consumer')
        );
    }


    /**
     * Update consumer.
     */
    public function update(
        UpdateConsumerRequest $request,
        Consumer $consumer
    ) {


        if (
            $consumer->registration_source === 'Self Registration' &&
            $consumer->verification_status !== 'Verified'
        ) {
            return redirect()
                ->route(
                    'customer-service.consumers.show',
                    $consumer
                )
                ->with(
                    'error',
                    $consumer->verification_status === 'Rejected'
                        ? 'This self-registration was rejected. The consumer must correct and resubmit the registration before it can be edited by Customer Service.'
                        : 'This self-registered consumer is awaiting administrator verification and cannot be edited yet.'
                );
        }
        $validated =
            $request->validated();


        DB::transaction(
            function () use (
                $validated,
                $consumer
            ) {

                /*
                |--------------------------------------------------------------------------
                | Consumer
                |--------------------------------------------------------------------------
                */

                $consumer->update([

                    'account_number' =>
                    $validated['account_number'],

                    'first_name' =>
                    $validated['first_name'],

                    'middle_name' =>
                    $validated['middle_name']
                        ?? null,

                    'last_name' =>
                    $validated['last_name'],

                    'suffix' =>
                    $validated['suffix']
                        ?? null,

                    'sex' =>
                    $validated['sex'],

                    'phone' =>
                    $validated['phone'],

                    'email' =>
                    $validated['email'],

                    'is_active' =>
                    (bool) $validated['is_active'],
                ]);


                /*
                |--------------------------------------------------------------------------
                | Registered Service Address
                |--------------------------------------------------------------------------
                */

                $consumer->address()->updateOrCreate(

                    [
                        'consumer_id' =>
                        $consumer->id,
                    ],

                    [
                        'house_no' =>
                        $validated['house_no']
                            ?? null,

                        'street' =>
                        $validated['street']
                            ?? null,

                        'purok' =>
                        $validated['purok']
                            ?? null,

                        'barangay' =>
                        $validated['barangay'],

                        'municipality' =>
                        $validated['municipality'],

                        'province' =>
                        $validated['province'],

                        /*
                        |--------------------------------------------------------------------------
                        | Registered Service Coordinates
                        |--------------------------------------------------------------------------
                        */

                        'latitude' =>
                        $validated['latitude'],

                        'longitude' =>
                        $validated['longitude'],
                    ]
                );


                /*
                |--------------------------------------------------------------------------
                | Portal Account
                |--------------------------------------------------------------------------
                */

                if ($consumer->user) {

                    $user =
                        $consumer->user;


                    $user->update([

                        'first_name' =>
                        $consumer->first_name,

                        'middle_name' =>
                        $consumer->middle_name,

                        'last_name' =>
                        $consumer->last_name,

                        'suffix' =>
                        $consumer->suffix,

                        'phone' =>
                        $consumer->phone,

                        'email' =>
                        $consumer->email,

                        'is_active' =>
                        $consumer->is_active,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Reset Password
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty($validated['new_password'])
                    ) {

                        $user->update([

                            'password' =>
                            Hash::make(
                                $validated['new_password']
                            ),
                        ]);
                    }
                }
            }
        );


        return redirect()
            ->route(
                'customer-service.consumers.show',
                $consumer
            )
            ->with(
                'success',
                'Consumer information updated successfully.'
            );
    }


    /**
     * Delete consumer.
     */
    public function destroy(
        Consumer $consumer
    ) {
        DB::transaction(
            function () use ($consumer) {

                $consumer->load([
                    'user',
                    'address',
                ]);


                $user =
                    $consumer->user;


                /*
                |--------------------------------------------------------------------------
                | Consumer Address
                |--------------------------------------------------------------------------
                */

                if ($consumer->address) {

                    $consumer->address
                        ->delete();
                }


                /*
                |--------------------------------------------------------------------------
                | Consumer
                |--------------------------------------------------------------------------
                */

                $consumer->forceDelete();


                /*
                |--------------------------------------------------------------------------
                | Portal User
                |--------------------------------------------------------------------------
                */

                if ($user) {

                    $user->syncRoles([]);

                    $user->forceDelete();
                }
            }
        );


        return redirect()
            ->route(
                'customer-service.consumers.index'
            )
            ->with(
                'success',
                'Consumer and portal account permanently deleted successfully.'
            );
    }
}
