<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with(['department'])
            ->withCount('users');

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('position_name', 'like', '%' . $request->search . '%')

                    ->orWhere('position_code', 'like', '%' . $request->search . '%')

                    ->orWhere('description', 'like', '%' . $request->search . '%')

                    ->orWhereHas('department', function ($department) use ($request) {

                        $department->where(
                            'department_name',
                            'like',
                            '%' . $request->search . '%'
                        );
                    });
            });
        }

        $positions = $query

            ->latest()

            ->paginate(10)

            ->withQueryString();

        return view('admin.positions.index', [

            'positions' => $positions,

            'totalPositions' => Position::count(),

            'activePositions' => Position::where('is_active', true)->count(),

            'inactivePositions' => Position::where('is_active', false)->count(),

            'assignedEmployees' => User::whereNotNull('position_id')->count(),

        ]);
    }

    public function create()
    {
        return view('admin.positions.create', [

            'position' => null,

            'departments' => Department::where('is_active', true)
                ->orderBy('department_name')
                ->get(),

        ]);
    }

    public function store(StorePositionRequest $request)
    {
        Position::create([

            'position_code' => Position::generateCode($request->position_name),

            'position_name' => $request->position_name,

            'department_id' => $request->department_id,

            'description' => $request->description,

            'is_active' => true,

        ]);

        return redirect()
            ->route('admin.positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        $position->load([

            'department',

            'users.department',

        ]);

        return view('admin.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        return view('admin.positions.edit', [

            'position' => $position,

            'departments' => Department::where('is_active', true)
                ->orderBy('department_name')
                ->get(),

        ]);
    }

    public function update(UpdatePositionRequest $request, Position $position)
    {
        $position->update([

            'position_name' => $request->position_name,

            'department_id' => $request->department_id,

            'description' => $request->description,

            'is_active' => $request->boolean('is_active'),

        ]);

        return redirect()

            ->route('admin.positions.index')

            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        if ($position->users()->count() > 0) {

            return back()->with(

                'error',

                'Cannot delete this position because it is assigned to employees.'

            );
        }

        $position->delete();

        return back()->with(

            'success',

            'Position deleted successfully.'

        );
    }
}
