<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerService\StoreComplaintCategoryRequest;
use App\Http\Requests\CustomerService\UpdateComplaintCategoryRequest;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use App\Models\Division;

class ComplaintCategoryController extends Controller
{
    /**
     * Display complaint categories.
     */
    public function index(Request $request)
    {
        $query = ComplaintCategory::query();

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status
        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status
            );
        }

        $categories = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'customer-service.complaint-categories.index',
            [
                'categories' => $categories,

                'totalCategories' => ComplaintCategory::count(),

                'activeCategories' => ComplaintCategory::where(
                    'is_active',
                    true
                )->count(),

                'inactiveCategories' => ComplaintCategory::where(
                    'is_active',
                    false
                )->count(),
            ]
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $divisions = Division::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'customer-service.complaint-categories.create',
            compact('divisions')
        );
    }


    /**
     * Store complaint category.
     */
    public function store(StoreComplaintCategoryRequest $request)
    {
        $validated = $request->validated();

        $deletedCategory = ComplaintCategory::onlyTrashed()
            ->where('name', $validated['name'])
            ->first();

        if ($deletedCategory) {

            $deletedCategory->restore();

            $deletedCategory->update([
                'division_id' => $validated['division_id'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            return redirect()
                ->route('customer-service.complaint-categories.index')
                ->with(
                    'success',
                    'The previously deleted complaint type has been restored.'
                );
        }

        $code = ComplaintCategory::generateCode(
            $validated['name']
        );

        ComplaintCategory::create([
            'division_id' => $validated['division_id'],
            'code' => $code,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('customer-service.complaint-categories.index')
            ->with(
                'success',
                'Complaint type created successfully.'
            );
    }


    /**
     * Show edit form.
     */
    public function edit(
        ComplaintCategory $complaintCategory
    ) {
        $divisions = Division::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'customer-service.complaint-categories.edit',
            compact(
                'complaintCategory',
                'divisions'
            )
        );
    }


    /**
     * Update complaint category.
     */
    public function update(
        UpdateComplaintCategoryRequest $request,
        ComplaintCategory $complaintCategory
    ) {
        $validated = $request->validated();

        /*
    |--------------------------------------------------------------------------
    | Regenerate code if complaint type name changed
    |--------------------------------------------------------------------------
    */

        $nameChanged = $complaintCategory->name !== $validated['name'];

        $data = [
            'division_id' => $validated['division_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if ($nameChanged) {
            $data['code'] = ComplaintCategory::generateCode(
                $validated['name']
            );
        }

        $complaintCategory->update($data);

        return redirect()
            ->route('customer-service.complaint-categories.index')
            ->with(
                'success',
                'Complaint type updated successfully.'
            );
    }


    /**
     * Soft delete complaint category.
     */
    public function destroy(
        ComplaintCategory $complaintCategory
    ) {
        /*
        |--------------------------------------------------------------------------
        | Prevent deletion if already used by a complaint
        |--------------------------------------------------------------------------
        */

        if ($complaintCategory->complaints()->exists()) {

            return back()->with(
                'error',
                'This category cannot be deleted because it is already being used by a complaint.'
            );
        }


        $complaintCategory->delete();


        return redirect()
            ->route(
                'customer-service.complaint-categories.index'
            )
            ->with(
                'success',
                'Complaint category deleted successfully.'
            );
    }
}
