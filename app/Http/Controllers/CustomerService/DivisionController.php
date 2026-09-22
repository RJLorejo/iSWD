<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerService\StoreDivisionRequest;
use App\Http\Requests\CustomerService\UpdateDivisionRequest;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display divisions.
     */
    public function index(Request $request)
    {
        $query = Division::query()
            ->withCount([
                'complaintTypes',
                'complaints',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
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


        /*
        |--------------------------------------------------------------------------
        | Records
        |--------------------------------------------------------------------------
        */

        $divisions = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalDivisions =
            Division::count();

        $activeDivisions =
            Division::where(
                'is_active',
                true
            )->count();

        $inactiveDivisions =
            Division::where(
                'is_active',
                false
            )->count();


        return view(
            'customer-service.divisions.index',
            compact(
                'divisions',
                'totalDivisions',
                'activeDivisions',
                'inactiveDivisions'
            )
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        return view(
            'customer-service.divisions.create'
        );
    }


    /**
     * Store division.
     */
    public function store(
        StoreDivisionRequest $request
    ) {
        $validated = $request->validated();


        Division::create([

            'name' =>
                $validated['name'],

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                true,

        ]);


        return redirect()
            ->route(
                'customer-service.divisions.index'
            )
            ->with(
                'success',
                'Division created successfully.'
            );
    }


    /**
     * Show edit form.
     */
    public function edit(
        Division $division
    ) {
        $division->loadCount([
            'complaintTypes',
            'complaints',
        ]);


        return view(
            'customer-service.divisions.edit',
            compact('division')
        );
    }


    /**
     * Update division.
     */
    public function update(
        UpdateDivisionRequest $request,
        Division $division
    ) {
        $validated = $request->validated();


        $division->update([

            'name' =>
                $validated['name'],

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $validated['is_active'],

        ]);


        return redirect()
            ->route(
                'customer-service.divisions.index'
            )
            ->with(
                'success',
                'Division updated successfully.'
            );
    }


    /**
     * Delete division.
     */
    public function destroy(
        Division $division
    ) {
        /*
        |--------------------------------------------------------------------------
        | Prevent deletion if complaint types belong to division
        |--------------------------------------------------------------------------
        */

        if ($division->complaintTypes()->exists()) {

            return back()->with(
                'error',
                'This division cannot be deleted because it has complaint types assigned to it.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent deletion if already used by complaints
        |--------------------------------------------------------------------------
        */

        if ($division->complaints()->exists()) {

            return back()->with(
                'error',
                'This division cannot be deleted because it is already being used by a complaint.'
            );
        }


        $division->delete();


        return redirect()
            ->route(
                'customer-service.divisions.index'
            )
            ->with(
                'success',
                'Division deleted successfully.'
            );
    }
}
