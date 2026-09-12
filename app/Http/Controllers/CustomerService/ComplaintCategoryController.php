<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerService\StoreComplaintCategoryRequest;
use App\Http\Requests\CustomerService\UpdateComplaintCategoryRequest;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;

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
        return view(
            'customer-service.complaint-categories.create'
        );
    }


    /**
     * Store complaint category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $deletedCategory = ComplaintCategory::onlyTrashed()
            ->where('name', $validated['name'])
            ->first();

        if ($deletedCategory) {

            $deletedCategory->restore();

            $deletedCategory->update([
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            return redirect()
                ->route('customer-service.complaint-categories.index')
                ->with(
                    'success',
                    'The previously deleted complaint category has been restored.'
                );
        }

        $code = ComplaintCategory::generateCode(
            $validated['name']
        );

        ComplaintCategory::create([
            'code' => $code,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('customer-service.complaint-categories.index')
            ->with(
                'success',
                'Complaint category created successfully.'
            );
    }


    /**
     * Show edit form.
     */
    public function edit(
        ComplaintCategory $complaintCategory
    ) {
        return view(
            'customer-service.complaint-categories.edit',
            compact('complaintCategory')
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

        $complaintCategory->update([

            'name' => $validated['name'],

            'description' => $validated['description'] ?? null,

            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route(
                'customer-service.complaint-categories.index'
            )
            ->with(
                'success',
                'Complaint category updated successfully.'
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
