<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplaintCategory;
use App\Http\Requests\Admin\StoreComplaintCategoryRequest;
use App\Http\Requests\Admin\UpdateComplaintCategoryRequest;

class ComplaintCategoryController extends Controller
{

    public function index(Request $request)
    {
        $categories = ComplaintCategory::withCount('complaints')

            ->when($request->search, function ($query) use ($request) {

                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            })

            ->latest()

            ->paginate(10)

            ->withQueryString();

        return view('admin.complaint-categories.index', [

            'categories' => $categories,

            'totalCategories' => ComplaintCategory::count(),

            'activeCategories' => ComplaintCategory::where('is_active', true)->count(),

            'inactiveCategories' => ComplaintCategory::where('is_active', false)->count(),

            'totalComplaints' => \App\Models\Complaint::count(),

        ]);
    }

    public function create()
    {
        $complaintCategory = null;

        return view(
            'admin.complaint-categories.create',
            compact('complaintCategory')
        );
    }

    public function store(StoreComplaintCategoryRequest $request)
    {
        ComplaintCategory::create([

            'code' => ComplaintCategory::generateCode(
                $request->name
            ),

            'name' => $request->name,

            'description' => $request->description,

            'is_active' => true,

        ]);

        return redirect()

            ->route('admin.complaint-categories.index')

            ->with(
                'success',
                'Complaint category created successfully.'
            );
    }

    public function show(ComplaintCategory $complaintCategory)
    {
        $complaintCategory->loadCount('complaints');

        return view(

            'admin.complaint-categories.show',

            compact('complaintCategory')

        );
    }

    public function edit(ComplaintCategory $complaintCategory)
    {
        return view(
            'admin.complaint-categories.edit',
            compact('complaintCategory')
        );
    }

    public function update(
        UpdateComplaintCategoryRequest $request,
        ComplaintCategory $complaintCategory
    ) {
        $complaintCategory->update([

            'name' => $request->name,

            'description' => $request->description,

            'is_active' => $request->boolean('is_active'),

        ]);

        return redirect()
            ->route('admin.complaint-categories.index')
            ->with('success', 'Complaint Category updated successfully.');
    }

    public function destroy(ComplaintCategory $complaintCategory)
    {
        $complaintCategory->delete();

        return back()->with(
            'success',
            'Complaint Category deleted successfully.'
        );
    }
}
