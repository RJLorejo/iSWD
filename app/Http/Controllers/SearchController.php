<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Consumer;
use App\Models\Department;
use App\Models\MaintenanceReport;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->q ?? '');

        /*
        |--------------------------------------------------------------------------
        | Empty collections
        |--------------------------------------------------------------------------
        */

        $users = collect();
        $departments = collect();
        $positions = collect();
        $consumers = collect();
        $complaints = collect();
        $maintenanceReports = collect();

        /*
        |--------------------------------------------------------------------------
        | Current User
        |--------------------------------------------------------------------------
        */

        $currentUser = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($keyword !== '') {

            /*
            |--------------------------------------------------------------------------
            | Employees
            |--------------------------------------------------------------------------
            */

            if ($this->canSearchEmployees($currentUser)) {

                $users = User::with([
                    'department',
                    'position',
                    'roles',
                ])
                    ->where(function ($query) use ($keyword) {

                        $query
                            ->where('employee_id', 'LIKE', "%{$keyword}%")
                            ->orWhere('first_name', 'LIKE', "%{$keyword}%")
                            ->orWhere('middle_name', 'LIKE', "%{$keyword}%")
                            ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                            ->orWhere('email', 'LIKE', "%{$keyword}%");
                    })
                    ->limit(10)
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Departments
            |--------------------------------------------------------------------------
            */

            if ($this->canSearchDepartments($currentUser)) {

                $departments = Department::where(
                    'department_name',
                    'LIKE',
                    "%{$keyword}%"
                )
                    ->limit(10)
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Positions
            |--------------------------------------------------------------------------
            */

            if ($this->canSearchPositions($currentUser)) {

                $positions = Position::where(
                    'position_name',
                    'LIKE',
                    "%{$keyword}%"
                )
                    ->limit(10)
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Consumers
            |--------------------------------------------------------------------------
            */

            if ($this->canSearchConsumers($currentUser)) {

                $consumerQuery = Consumer::query();

                $consumerQuery->where(function ($query) use ($keyword) {

                    $query
                        ->where('first_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('middle_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%")
                        ->orWhere('phone', 'LIKE', "%{$keyword}%");
                });

                /*
                |------------------------------------------------------------------
                | Consumer can only search their own record
                |------------------------------------------------------------------
                */

                if ($this->isConsumer($currentUser)) {

                    $consumerQuery->where(
                        'id',
                        $currentUser->consumer_id
                    );
                }

                $consumers = $consumerQuery
                    ->limit(10)
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Complaints
            |--------------------------------------------------------------------------
            */

            if ($this->canSearchComplaints($currentUser)) {

                $complaintQuery = Complaint::with([
                    'consumer',
                    'category',
                    'technicians',
                ]);

                $complaintQuery->where(function ($query) use ($keyword) {

                    $query
                        ->where('complaint_no', 'LIKE', "%{$keyword}%")
                        ->orWhere('subject', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->orWhere('complainant_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('complainant_phone', 'LIKE', "%{$keyword}%")
                        ->orWhere('address', 'LIKE', "%{$keyword}%")
                        ->orWhere('landmark', 'LIKE', "%{$keyword}%");
                });

                /*
                |------------------------------------------------------------------
                | Technician
                | Only assigned complaints
                |------------------------------------------------------------------
                */

                if ($this->isMaintenanceTechnician($currentUser)) {

                    $complaintQuery->where(
                        'assigned_to',
                        $currentUser->id
                    );
                }

                /*
                |------------------------------------------------------------------
                | Consumer
                | Only own complaints
                |------------------------------------------------------------------
                */

                if ($this->isConsumer($currentUser)) {

                    $complaintQuery->where(
                        'consumer_id',
                        $currentUser->consumer_id
                    );
                }

                $complaints = $complaintQuery
                    ->latest()
                    ->limit(15)
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Maintenance Reports
            |--------------------------------------------------------------------------
            */

            if ($this->canSearchMaintenanceReports($currentUser)) {

                $reportQuery = MaintenanceReport::with([
                    'complaint',
                    'technician',
                ]);

                $reportQuery->where(function ($query) use ($keyword) {

                    $query
                        ->where('diagnosis', 'LIKE', "%{$keyword}%")
                        ->orWhere('root_cause', 'LIKE', "%{$keyword}%")
                        ->orWhere('work_performed', 'LIKE', "%{$keyword}%")
                        ->orWhere('repair_procedure', 'LIKE', "%{$keyword}%")
                        ->orWhere('materials_used', 'LIKE', "%{$keyword}%")
                        ->orWhere('parts_replaced', 'LIKE', "%{$keyword}%")
                        ->orWhere('tools_used', 'LIKE', "%{$keyword}%")
                        ->orWhere('technician_notes', 'LIKE', "%{$keyword}%")
                        ->orWhere('completion_remarks', 'LIKE', "%{$keyword}%");
                });

                /*
                |------------------------------------------------------------------
                | Technician
                | Only own reports
                |------------------------------------------------------------------
                */

                if ($this->isMaintenanceTechnician($currentUser)) {

                    $reportQuery->where(
                        'technician_id',
                        $currentUser->id
                    );
                }

                /*
                |------------------------------------------------------------------
                | Consumer
                | Only reports belonging to own complaints
                |------------------------------------------------------------------
                */

                if ($this->isConsumer($currentUser)) {

                    $reportQuery->whereHas(
                        'complaint',
                        function ($query) use ($currentUser) {

                            $query->where(
                                'consumer_id',
                                $currentUser->consumer_id
                            );
                        }
                    );
                }

                $maintenanceReports = $reportQuery
                    ->latest()
                    ->limit(15)
                    ->get();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Role-Specific Layout
        |--------------------------------------------------------------------------
        */

        $layout = $this->resolveLayout($currentUser);


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('search.index', compact(
            'keyword',
            'users',
            'departments',
            'positions',
            'consumers',
            'complaints',
            'maintenanceReports',
            'layout'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    private function resolveLayout($user): string
    {
        if ($user->hasRole('Admin')) {
            return 'admin.layouts.app';
        }

        if ($user->hasRole('Customer Service')) {
            return 'customer-service.layouts.app';
        }

        if ($user->hasRole('Maintenance Manager')) {
            return 'maintenance-manager.layouts.app';
        }

        if ($user->hasRole('Maintenance Technician')) {
            return 'technician.layouts.app';
        }

        if ($user->hasRole('Consumer')) {
            return 'consumer.layouts.app';
        }

        return 'admin.layouts.app';
    }


    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    private function isAdmin($user): bool
    {
        return $user->hasRole('Admin');
    }

    private function isCustomerService($user): bool
    {
        return $user->hasRole('Customer Service');
    }

    private function isMaintenanceManager($user): bool
    {
        return $user->hasRole('Maintenance Manager');
    }

    private function isMaintenanceTechnician($user): bool
    {
        return $user->hasRole('Maintenance Technician');
    }

    private function isConsumer($user): bool
    {
        return $user->hasRole('Consumer');
    }


    /*
    |--------------------------------------------------------------------------
    | Search Permissions
    |--------------------------------------------------------------------------
    */

    private function canSearchEmployees($user): bool
    {
        return $this->isAdmin($user)
            || $this->isMaintenanceManager($user);
    }

    private function canSearchDepartments($user): bool
    {
        return $this->isAdmin($user)
            || $this->isMaintenanceManager($user);
    }

    private function canSearchPositions($user): bool
    {
        return $this->isAdmin($user)
            || $this->isMaintenanceManager($user);
    }

    private function canSearchConsumers($user): bool
    {
        return $this->isAdmin($user)
            || $this->isCustomerService($user)
            || $this->isMaintenanceManager($user)
            || $this->isMaintenanceTechnician($user)
            || $this->isConsumer($user);
    }

    private function canSearchComplaints($user): bool
    {
        return $this->isAdmin($user)
            || $this->isCustomerService($user)
            || $this->isMaintenanceManager($user)
            || $this->isMaintenanceTechnician($user)
            || $this->isConsumer($user);
    }

    private function canSearchMaintenanceReports($user): bool
    {
        return $this->isAdmin($user)
            || $this->isMaintenanceManager($user)
            || $this->isMaintenanceTechnician($user)
            || $this->isCustomerService($user)
            || $this->isConsumer($user);
    }
}
