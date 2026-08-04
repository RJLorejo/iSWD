<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('users');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('department_name', 'like', "%{$search}%")
                    ->orWhere('department_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {

            $query->where('is_active', $request->status);
        }

        $departments = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.departments.index', [

            'departments'          => $departments,
            'totalDepartments'     => Department::count(),
            'activeDepartments'    => Department::where('is_active', true)->count(),
            'inactiveDepartments'  => Department::where('is_active', false)->count(),
            'assignedEmployees'    => User::count(),

        ]);
    }

    public function create()
    {
        return view('admin.departments.create', [
            'department' => new Department(),
        ]);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $next = Department::max('id') + 1;

        $departmentCode = 'DEPT-' . str_pad($next, 3, '0', STR_PAD_LEFT);

        Department::create([

            'department_code' => Department::generateCode($request->department_name),

            'department_name' => $request->department_name,

            'description' => $request->description,

            'is_active' => true,

        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load('users');

        return view(
            'admin.departments.show',
            compact('department')
        );
    }

    public function edit(Department $department)
    {
        return view(
            'admin.departments.edit',
            compact('department')
        );
    }

    public function update(
        UpdateDepartmentRequest $request,
        Department $department
    ) {
        $department->update([

            'department_name' => $request->department_name,

            'description' => $request->description,

            'is_active' => $request->boolean('is_active'),

        ]);

        return redirect()

            ->route('admin.departments.index')

            ->with(
                'success',
                'Department updated successfully.'
            );
    }
    public function destroy(Department $department)
    {
        if ($department->users()->count()) {

            return back()->with(

                'error',

                'Cannot delete a department with assigned employees.'

            );
        }

        $department->delete();

        return redirect()

            ->route('admin.departments.index')

            ->with(
                'success',
                'Department deleted successfully.'
            );
    }

    public function toggle(Department $department)
    {
        $department->update([

            'is_active' => !$department->is_active

        ]);

        return back();
    }
}
