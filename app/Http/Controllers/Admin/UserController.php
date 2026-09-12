<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['department', 'position', 'roles']);

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_id', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department) {

            $query->where('department_id', $request->department);
        }

        if ($request->position) {

            $query->where('position_id', $request->position);
        }

        if ($request->status != '') {

            $query->where('is_active', $request->status);
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [

            'users' => $users,

            'departments' => Department::pluck(
                'department_name',
                'id'
            ),

            'positions' => Position::pluck(
                'position_name',
                'id'
            ),

            'totalEmployees' => User::count(),

            'activeEmployees' => User::where(
                'is_active',
                1
            )->count(),

            'inactiveEmployees' => User::where(
                'is_active',
                0
            )->count(),

            'maintenance_managers' => User::role(
                'Maintenance Manager'
            )->count(),

            'maintenance_technicians' => User::role(
                'Maintenance Technician'
            )->count(),

            'customer_services' => User::role(
                'Customer Service'
            )->count(),

        ]);
    }

    public function create()
    {
        $departments = Department::pluck(
            'department_name',
            'id'
        );

        $positions = Position::pluck(
            'position_name',
            'id'
        );

        $roles = Role::pluck(
            'name',
            'name'
        );

        return view(
            'admin.users.create',
            compact(
                'departments',
                'positions',
                'roles'
            )
        );
    }

    public function store(StoreUserRequest $request)
    {

        $password = Str::random(10);

        $avatar = null;

        if ($request->hasFile('avatar')) {

            $avatar = $request
                ->file('avatar')
                ->store('avatars', 'public');
        }

        $user = User::create([

            'employee_id' => $request->employee_id,

            'first_name' => $request->first_name,

            'middle_name' => $request->middle_name,

            'last_name' => $request->last_name,

            'suffix' => $request->suffix,

            'email' => $request->email,

            'phone' => $request->phone,

            'department_id' => $request->department_id,

            'position_id' => $request->position_id,

            'avatar' => $avatar,

            'password' => Hash::make($password),

            'is_active' => true,

        ]);

        $user->assignRole($request->role);

        return redirect()

            ->route('admin.users.index')

            ->with(

                'success',

                'Employee created successfully.<br>Temporary Password: ' . $password

            );
    }

    public function show(User $user)
    {
        $user->load([
            'department',
            'position',
            'roles'
        ]);

        return view(
            'admin.users.show',
            compact('user')
        );
    }

    public function resetPassword(User $user)
    {

        $password = Str::random(10);

        $user->update([

            'password' => Hash::make($password)

        ]);

        return back()

            ->with(

                'success',

                'Temporary Password: ' . $password

            );
    }

    public function edit(User $user)
    {
        return view(
            'admin.users.edit',
            [

                'user' => $user,

                'departments' => Department::pluck(
                    'department_name',
                    'id'
                ),

                'positions' => Position::pluck(
                    'position_name',
                    'id'
                ),

                'roles' => Role::pluck(
                    'name',
                    'name'
                )

            ]
        );
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $avatar = $user->avatar;

        if ($request->hasFile('avatar')) {

            $avatar = $request
                ->file('avatar')
                ->store('avatars', 'public');
        }

        $user->update([

            'employee_id' => $request->employee_id,

            'first_name' => $request->first_name,

            'middle_name' => $request->middle_name,

            'last_name' => $request->last_name,

            'suffix' => $request->suffix,

            'email' => $request->email,

            'phone' => $request->phone,

            'department_id' => $request->department_id,

            'position_id' => $request->position_id,

            'avatar' => $avatar,

            'is_active' => $request->boolean('is_active'),

        ]);

        $user->syncRoles([$request->role]);

        return redirect()

            ->route('admin.users.index')

            ->with(
                'success',
                'Employee updated successfully.'
            );
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()

            ->route('admin.users.index')

            ->with(
                'success',
                'Employee deleted successfully.'
            );
    }

    public function toggle(User $user)
    {

        $user->update([

            'is_active' => !$user->is_active

        ]);

        return back();
    }
}
