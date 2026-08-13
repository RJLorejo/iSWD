<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsumerRequest;
use App\Http\Requests\UpdateConsumerRequest;
use App\Models\Consumer;
use App\Models\ConsumerAddress;
use App\Models\ServiceConnection;
use App\Models\ServiceAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ConsumerController extends Controller
{
    public function index(Request $request)
    {
        $query = Consumer::with([
            'address',
            'serviceConnections.address',
            'user'
        ]);

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('consumer_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")

                    ->orWhereHas('serviceConnections', function ($connection) use ($search) {

                        $connection
                            ->where('account_number', 'like', "%{$search}%")
                            ->orWhere('meter_number', 'like', "%{$search}%")
                            ->orWhere(
                                'service_connection_number',
                                'like',
                                "%{$search}%"
                            );
                    });
            });
        }

        if ($request->filled('status')) {

            $query->where('is_active', $request->status);
        }

        $consumers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer-service.consumers.index', [

            'consumers' => $consumers,

            'totalConsumers' => Consumer::count(),

            'activeConsumers' => Consumer::where('is_active', true)->count(),

            'inactiveConsumers' => Consumer::where('is_active', false)->count(),

            'todayConsumers' => Consumer::whereDate('created_at', today())->count(),

        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('customer-service.consumers.create');
    }

    /**
     * Store Consumer.
     */
    public function store(StoreConsumerRequest $request)
    {
        DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | Create Portal Account (Optional)
            |--------------------------------------------------------------------------
            */

            $user = null;

            if ($request->boolean('create_account')) {

                $user = User::create([

                    'employee_id' => null,

                    'first_name' => $request->first_name,

                    'middle_name' => $request->middle_name,

                    'last_name' => $request->last_name,

                    'suffix' => $request->suffix,

                    'phone' => $request->phone,

                    'email' => $request->email,

                    'password' => Hash::make('Temp@12345'),

                    'is_active' => true,

                ]);

                $user->assignRole('Consumer');
            }

            /*
            |--------------------------------------------------------------------------
            | Consumer
            |--------------------------------------------------------------------------
            */

            $consumer = Consumer::create([

                'consumer_no' => Consumer::generateConsumerNo(),

                'user_id' => $user?->id,

                'first_name' => $request->first_name,

                'middle_name' => $request->middle_name,

                'last_name' => $request->last_name,

                'suffix' => $request->suffix,

                'sex' => $request->sex,

                'birth_date' => $request->birth_date,

                'phone' => $request->phone,

                'email' => $request->email,

                'is_active' => true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Residential Address
            |--------------------------------------------------------------------------
            */
            ConsumerAddress::create([

                'consumer_id' => $consumer->id,

                'house_no' => $request->house_no,

                'street' => $request->street,

                'purok' => $request->purok,

                'barangay' => $request->barangay,

                'municipality' => $request->municipality ?? 'Sagay',

                'province' => $request->province ?? 'Negros Occidental',

                'zip_code' => $request->zip_code,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Service Connection
            |--------------------------------------------------------------------------
            */

            $connection = ServiceConnection::create([

                'consumer_id' => $consumer->id,

                'account_number' => $request->account_number,

                'service_connection_number' =>
                $request->service_connection_number
                    ?: ServiceConnection::generateConnectionNumber(),

                'meter_number' => $request->meter_number,

                'status' => $request->status ?? 'Pending',

                'installation_date' => $request->installation_date,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Service Address
            |--------------------------------------------------------------------------
            */
            ServiceAddress::create([

                'service_connection_id' => $connection->id,

                'house_no' => $request->service_house_no,

                'street' => $request->service_street,

                'purok' => $request->service_purok,

                'barangay' => $request->service_barangay,

                'city' => $request->service_city,

                'province' => $request->service_province,

                'zip_code' => $request->service_zip_code,

                'landmark' => $request->landmark,

            ]);
        });

        return redirect()

            ->route('customer-service.consumers.index')

            ->with('success', 'Consumer registered successfully.');
    }

    public function show(Consumer $consumer)
    {
        $consumer->load([
            'address',
            'serviceConnections.address',
            'user',
        ]);

        return view('customer-service.consumers.show', compact('consumer'));
    }

    public function edit(Consumer $consumer)
    {
        $consumer->load([
            'address',
            'serviceConnections.address',
        ]);

        // For now, edit the first service connection.
        // Later, we can support multiple connections from the UI.
        $connection = $consumer->serviceConnections->first();

        return view('customer-service.consumers.edit', compact(
            'consumer',
            'connection'
        ));
    }

    public function update(
        UpdateConsumerRequest $request,
        Consumer $consumer
    ) {
        DB::transaction(function () use ($request, $consumer) {

            /*
        |--------------------------------------------------------------------------
        | Consumer
        |--------------------------------------------------------------------------
        */

            $consumer->update([

                'first_name' => $request->first_name,

                'middle_name' => $request->middle_name,

                'last_name' => $request->last_name,

                'suffix' => $request->suffix,

                'sex' => $request->sex,

                'birth_date' => $request->birth_date,

                'phone' => $request->phone,

                'email' => $request->email,

                'is_active' => $request->boolean('is_active'),

            ]);


            /*
        |--------------------------------------------------------------------------
        | Residential Address
        |--------------------------------------------------------------------------
        */

            $consumer->address()->updateOrCreate(

                [
                    'consumer_id' => $consumer->id,
                ],

                [

                    'house_no' => $request->house_no,

                    'street' => $request->street,

                    'purok' => $request->purok,

                    'barangay' => $request->barangay,

                    'municipality' => $request->municipality ?? 'Sagay',

                    'province' => $request->province ?? 'Negros Occidental',

                    'zip_code' => $request->zip_code,

                ]

            );


            /*
        |--------------------------------------------------------------------------
        | Service Connection
        |--------------------------------------------------------------------------
        */

            $connection = $consumer->serviceConnections()->first();

            if ($connection) {

                $connection->update([

                    'account_number' => $request->account_number,

                    'meter_number' => $request->meter_number,

                    'connection_type' => $request->connection_type,

                    'meter_size' => $request->meter_size,

                    'status' => $request->connection_status,

                    'installation_date' => $request->installation_date,



                    'remarks' => $request->remarks,

                ]);


                /*
            |--------------------------------------------------------------------------
            | Service Address
            |--------------------------------------------------------------------------
            */

                $connection->address()->updateOrCreate(

                    [
                        'service_connection_id' => $connection->id,
                    ],

                    [

                        'house_no' => $request->service_house_no,

                        'street' => $request->service_street,

                        'purok' => $request->service_purok,

                        'barangay' => $request->service_barangay,

                        'city' => $request->service_city ?? 'Sagay City',

                        'province' => $request->service_province ?? 'Negros Occidental',

                        'zip_code' => $request->service_zip_code,

                        'landmark' => $request->landmark,

                    ]

                );

                /*
|--------------------------------------------------------------------------
| Consumer Portal Account
|--------------------------------------------------------------------------
*/

                if ($request->boolean('create_account') && !$consumer->user) {

                    $user = User::create([

                        'employee_id' => null,

                        'first_name' => $consumer->first_name,

                        'middle_name' => $consumer->middle_name,

                        'last_name' => $consumer->last_name,

                        'suffix' => $consumer->suffix,

                        'phone' => $consumer->phone,

                        'email' => $consumer->email,

                        'password' => Hash::make('Temp@12345'),

                        'is_active' => true,

                    ]);

                    $user->assignRole('Consumer');

                    $consumer->update([
                        'user_id' => $user->id,
                    ]);
                }
            }
        });



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

    public function destroy(Consumer $consumer)
    {
        DB::transaction(function () use ($consumer) {

            // Delete portal account if connected
            if ($consumer->user) {
                $consumer->user->delete();
            }

            // Delete residential address
            if ($consumer->address) {
                $consumer->address->delete();
            }

            // Delete service connections and their addresses
            foreach ($consumer->serviceConnections as $connection) {

                if ($connection->address) {
                    $connection->address->delete();
                }

                $connection->delete();
            }

            // Soft delete consumer
            $consumer->delete();
        });

        return redirect()
            ->route('customer-service.consumers.index')
            ->with('success', 'Consumer deleted successfully.');
    }
}

