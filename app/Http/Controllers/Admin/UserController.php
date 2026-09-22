<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Employee management.
     *
     * Consumer accounts are intentionally excluded.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Employee Query
        |--------------------------------------------------------------------------
        |
        | Admin User Management is for employee accounts only.
        |
        | Consumer accounts are managed separately through:
        | - Consumer Verifications
        | - Customer Service Consumer Management
        |
        */

        $query = User::query()
            ->with([
                'department',
                'position',
                'roles',
            ])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Consumer');
            });


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
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
                        'employee_id',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Department Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('department')) {

            $query->where(
                'department_id',
                $request->department
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Position Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('position')) {

            $query->where(
                'position_id',
                $request->position
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Role Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('role')) {

            $query->whereHas(
                'roles',
                function ($q) use ($request) {

                    $q->where(
                        'name',
                        $request->role
                    );
                }
            );
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
        | Employee Records
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Employee Roles
        |--------------------------------------------------------------------------
        |
        | Consumer is intentionally excluded.
        |
        */

        $roles = Role::query()
            ->where('name', '!=', 'Consumer')
            ->orderBy('name')
            ->pluck('name', 'name');


        /*
        |--------------------------------------------------------------------------
        | Employee Statistics
        |--------------------------------------------------------------------------
        */

        $employeeQuery = User::query()
            ->whereDoesntHave(
                'roles',
                function ($q) {
                    $q->where(
                        'name',
                        'Consumer'
                    );
                }
            );


        $totalEmployees = (clone $employeeQuery)
            ->count();


        $activeEmployees = (clone $employeeQuery)
            ->where(
                'is_active',
                true
            )
            ->count();


        $inactiveEmployees = (clone $employeeQuery)
            ->where(
                'is_active',
                false
            )
            ->count();


        $maintenanceManagers = User::role(
            'Maintenance Manager'
        )->count();


        $maintenanceTechnicians = User::role(
            'Maintenance Technician'
        )->count();


        $customerServices = User::role(
            'Customer Service'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.users.index',
            [
                'users' => $users,

                'departments' => Department::query()
                    ->orderBy('department_name')
                    ->pluck(
                        'department_name',
                        'id'
                    ),

                'positions' => Position::query()
                    ->orderBy('position_name')
                    ->pluck(
                        'position_name',
                        'id'
                    ),

                'roles' => $roles,

                'totalEmployees' =>
                $totalEmployees,

                'activeEmployees' =>
                $activeEmployees,

                'inactiveEmployees' =>
                $inactiveEmployees,

                'maintenance_managers' =>
                $maintenanceManagers,

                'maintenance_technicians' =>
                $maintenanceTechnicians,

                'customer_services' =>
                $customerServices,
            ]
        );
    }


    /**
     * Show employee creation form.
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | Employee Roles Only
        |--------------------------------------------------------------------------
        */

        $roles = Role::query()
            ->where(
                'name',
                '!=',
                'Consumer'
            )
            ->orderBy('name')
            ->pluck(
                'name',
                'name'
            );


        return view(
            'admin.users.create',
            [
                'departments' => Department::query()
                    ->orderBy('department_name')
                    ->pluck(
                        'department_name',
                        'id'
                    ),

                'positions' => Position::query()
                    ->orderBy('position_name')
                    ->pluck(
                        'position_name',
                        'id'
                    ),

                'roles' => $roles,
            ]
        );
    }


    /**
     * Store employee.
     */
    public function store(StoreUserRequest $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Extra Protection
        |--------------------------------------------------------------------------
        |
        | Even if someone manually modifies the form request,
        | Consumer accounts cannot be created here.
        |
        */

        if ($request->role === 'Consumer') {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Consumer accounts cannot be created through Employee Management.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Temporary Password
        |--------------------------------------------------------------------------
        */

        $password = Str::random(10);


        /*
        |--------------------------------------------------------------------------
        | Avatar
        |--------------------------------------------------------------------------
        */

        $avatar = null;

        if ($request->hasFile('avatar')) {

            $avatar = $request
                ->file('avatar')
                ->store(
                    'avatars',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Employee
        |--------------------------------------------------------------------------
        */

        $user = User::create([

            'employee_id' =>
            $request->employee_id,

            'first_name' =>
            $request->first_name,

            'middle_name' =>
            $request->middle_name,

            'last_name' =>
            $request->last_name,

            'suffix' =>
            $request->suffix,

            'email' =>
            $request->email,

            'phone' =>
            $request->phone,

            'department_id' =>
            $request->department_id,

            'position_id' =>
            $request->position_id,

            'avatar' =>
            $avatar,

            /*
             * Hash::make() is safe.
             *
             * Your User model also has a hashed cast, but Laravel
             * recognizes an already hashed password.
             */

            'password' =>
            Hash::make($password),

            'is_active' =>
            true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Employee Role
        |--------------------------------------------------------------------------
        */

        $user->assignRole(
            $request->role
        );


        return redirect()
            ->route(
                'admin.users.index'
            )
            ->with(
                'success',
                'Employee created successfully.<br>Temporary Password: '
                    . $password
            );
    }


    /**
     * Show employee.
     */
    public function show(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Consumer Access Through Employee Management
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            abort(404);
        }


        $user->load([
            'department',
            'position',
            'roles',
        ]);


        return view(
            'admin.users.show',
            compact('user')
        );
    }


    /**
     * Reset employee password.
     */
    public function resetPassword(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Employee Accounts Only
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            abort(404);
        }


        $password = Str::random(10);


        $user->update([
            'password' =>
            Hash::make($password),
        ]);


        return back()
            ->with(
                'success',
                'Temporary Password: '
                    . $password
            );
    }


    /**
     * Show employee edit form.
     */
    public function edit(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Consumer Accounts Are Managed Elsewhere
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Employee Roles Only
        |--------------------------------------------------------------------------
        */

        $roles = Role::query()
            ->where(
                'name',
                '!=',
                'Consumer'
            )
            ->orderBy('name')
            ->pluck(
                'name',
                'name'
            );


        return view(
            'admin.users.edit',
            [
                'user' =>
                $user,

                'departments' => Department::query()
                    ->orderBy('department_name')
                    ->pluck(
                        'department_name',
                        'id'
                    ),

                'positions' => Position::query()
                    ->orderBy('position_name')
                    ->pluck(
                        'position_name',
                        'id'
                    ),

                'roles' =>
                $roles,
            ]
        );
    }


    /**
     * Update employee.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ) {
        /*
        |--------------------------------------------------------------------------
        | Employee Accounts Only
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Consumer Role Assignment
        |--------------------------------------------------------------------------
        */

        if ($request->role === 'Consumer') {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The Consumer role cannot be assigned through Employee Management.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Avatar
        |--------------------------------------------------------------------------
        */

        $avatar = $user->avatar;

        if ($request->hasFile('avatar')) {

            /*
             * Delete previous uploaded avatar.
             */

            if ($avatar) {

                Storage::disk('public')
                    ->delete($avatar);
            }


            $avatar = $request
                ->file('avatar')
                ->store(
                    'avatars',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Employee
        |--------------------------------------------------------------------------
        */

        $user->update([

            'employee_id' =>
            $request->employee_id,

            'first_name' =>
            $request->first_name,

            'middle_name' =>
            $request->middle_name,

            'last_name' =>
            $request->last_name,

            'suffix' =>
            $request->suffix,

            'email' =>
            $request->email,

            'phone' =>
            $request->phone,

            'department_id' =>
            $request->department_id,

            'position_id' =>
            $request->position_id,

            'avatar' =>
            $avatar,

            'is_active' =>
            $request->boolean(
                'is_active'
            ),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Synchronize Employee Role
        |--------------------------------------------------------------------------
        */

        $user->syncRoles([
            $request->role,
        ]);


        return redirect()
            ->route(
                'admin.users.index'
            )
            ->with(
                'success',
                'Employee updated successfully.'
            );
    }


    /**
     * Delete employee.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Consumer Accounts Cannot Be Deleted Here
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Administrator From Deleting Own Account
        |--------------------------------------------------------------------------
        */

        if (
            auth()->id()
            === $user->id
        ) {
            return back()->with(
                'error',
                'You cannot delete your own administrator account.'
            );
        }


        $user->delete();


        return redirect()
            ->route(
                'admin.users.index'
            )
            ->with(
                'success',
                'Employee deleted successfully.'
            );
    }


    /**
     * Toggle employee account status.
     */
    public function toggle(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Employee Accounts Only
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Consumer')) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Administrator From Disabling Own Account
        |--------------------------------------------------------------------------
        */

        if (
            auth()->id()
            === $user->id
        ) {
            return back()->with(
                'error',
                'You cannot disable your own administrator account.'
            );
        }


        $user->update([
            'is_active' =>
            !$user->is_active,
        ]);


        return back()->with(
            'success',
            $user->is_active
                ? 'Employee account activated successfully.'
                : 'Employee account deactivated successfully.'
        );
    }

    public function print(Request $request)
    {
        $query = User::query()
            ->with([
                'department',
                'position',
                'roles',
            ])
            ->whereDoesntHave(
                'roles',
                fn($query) =>
                $query->where('name', 'Consumer')
            );

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'employee_id',
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
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('department')) {

            $query->where(
                'department_id',
                $request->department
            );
        }

        if ($request->filled('position')) {

            $query->where(
                'position_id',
                $request->position
            );
        }

        if ($request->filled('role')) {

            $query->role($request->role);
        }

        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status === '1'
            );
        }

        $users = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $totalEmployees = $users->count();

        $activeEmployees = $users
            ->where('is_active', true)
            ->count();

        $inactiveEmployees = $users
            ->where('is_active', false)
            ->count();

        return view(
            'admin.users.print',
            compact(
                'users',
                'totalEmployees',
                'activeEmployees',
                'inactiveEmployees'
            )
        );
    }
}
