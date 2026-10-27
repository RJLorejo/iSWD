<?php

use Illuminate\Support\Facades\Route;
use App\Models\Complaint;
use App\Models\Consumer;
use Illuminate\Http\Request;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ConsumerVerificationController;
use App\Http\Controllers\Admin\AdminSystemReportController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\ConsumerFeedbackController as AdminConsumerFeedbackController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ConsumerController as AdminConsumerController;

use App\Http\Controllers\CustomerService\DashboardController as CustomerServiceDashboard;
use App\Http\Controllers\CustomerService\ComplaintController as CustomerServiceComplaintController;
use App\Http\Controllers\CustomerService\ConsumerController;
use App\Http\Controllers\CustomerService\ComplaintCategoryController;
use App\Http\Controllers\CustomerService\ComplaintVerificationController;
use App\Http\Controllers\CustomerService\DivisionController;
use App\Http\Controllers\CustomerService\ConsumerFeedbackController;
use App\Http\Controllers\CustomerService\ServiceAnnouncementController as CustomerServiceAnnouncementController;
use App\Http\Controllers\CustomerService\ReportsAnalyticsController;
use App\Http\Controllers\CustomerService\FinalClosureController;
use App\Http\Controllers\CustomerService\MaintenanceMonitoringController;
use App\Http\Controllers\Admin\MaintenanceMonitoringController as AdminMaintenanceMonitoringController;


use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Manager\ComplaintController as ManagerComplaintController;
use App\Http\Controllers\Manager\MaintenanceReviewController;
use App\Http\Controllers\Manager\ServiceAreaController;
use App\Http\Controllers\Manager\MaintenanceScheduleController;
use App\Http\Controllers\Manager\SidebarCountController as ManagerSidebarCountController;



use App\Http\Controllers\Technician\DashboardController as TechnicianDashboard;
use App\Http\Controllers\Technician\ComplaintController as TechnicianComplaintController;
use App\Http\Controllers\Technician\MaintenanceReportController;
use App\Http\Controllers\Technician\SidebarCountController as TechnicianSidebarCountController;


use App\Http\Controllers\Consumer\DashboardController as ConsumerDashboardController;
use App\Http\Controllers\Consumer\ComplaintController as ConsumerComplaintController;
use App\Http\Controllers\Consumer\Auth\LoginController as ConsumerLoginController;
use App\Http\Controllers\Consumer\Auth\RegisterController as ConsumerRegisterController;
use App\Http\Controllers\Consumer\Auth\VerifyEmailController;
use App\Http\Controllers\Consumer\Auth\ForgotPasswordController;
use App\Http\Controllers\Consumer\Auth\ResetPasswordController;
use App\Http\Controllers\Consumer\ServiceAnnouncementController;
use App\Http\Controllers\Consumer\AIController;
use App\Http\Controllers\Consumer\RegistrationStatusController;
use App\Http\Controllers\Consumer\ComplaintFeedbackController;
use App\Http\Controllers\Consumer\ProfileController as ConsumerProfileController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])
    ->name('landing');


/*
|--------------------------------------------------------------------------
| GENERAL AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
|
| These are shared by all authenticated users.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

});


/*
|--------------------------------------------------------------------------
| ADMINISTRATOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Administrator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboard::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/users/print/report',
            [UserController::class, 'print']
        )->name('users.print');


        Route::resource('users', UserController::class);

        Route::post(
            '/users/{user}/reset-password',
            [UserController::class, 'resetPassword']
        )->name('users.reset');

        Route::post(
            '/users/{user}/toggle',
            [UserController::class, 'toggle']
        )->name('users.toggle');




        /*
        |--------------------------------------------------------------------------
        | Departments
        |--------------------------------------------------------------------------
        */

        Route::resource('departments', DepartmentController::class);

        Route::post(
            '/departments/{department}/toggle',
            [DepartmentController::class, 'toggle']
        )->name('departments.toggle');


        /*
        |--------------------------------------------------------------------------
        | Positions
        |--------------------------------------------------------------------------
        */

        Route::resource('positions', PositionController::class);

        /*
|--------------------------------------------------------------------------
| Consumer Registration Verification
|--------------------------------------------------------------------------
*/

        Route::get(
            '/consumer-verifications',
            [ConsumerVerificationController::class, 'index']
        )->name('consumer-verifications.index');


        Route::get('/sidebar-notifications', function () {
            $pendingConsumerVerifications = Consumer::query()
                ->where('registration_source', 'Self Registration')
                ->where('verification_status', 'Pending Verification')
                ->count();

            return response()->json([
                'pending_consumer_verifications' => $pendingConsumerVerifications,
            ]);
        })->name('sidebar.notifications');


        Route::get(
            '/consumer-verifications/{consumer}',
            [ConsumerVerificationController::class, 'show']
        )->name('consumer-verifications.show');


        Route::patch(
            '/consumer-verifications/{consumer}/approve',
            [ConsumerVerificationController::class, 'approve']
        )->name('consumer-verifications.approve');


        Route::patch(
            '/consumer-verifications/{consumer}/reject',
            [ConsumerVerificationController::class, 'reject']
        )->name('consumer-verifications.reject');

        Route::get('/reports', [
            AdminSystemReportController::class,
            'index',
        ])->name('reports.index');

        Route::get('/reports/print', [
            AdminSystemReportController::class,
            'print',
        ])->name('reports.print');



        Route::get('/complaints', [AdminComplaintController::class, 'index'])
            ->name('complaints.index');

        Route::get('/complaints/report', [AdminComplaintController::class, 'report'])
            ->name('complaints.report');

        Route::get('/complaints/{complaint}', [AdminComplaintController::class, 'show'])
            ->name('complaints.show');


        Route::prefix('maintenance-monitoring')
            ->name('maintenance-monitoring.')
            ->controller(AdminMaintenanceMonitoringController::class)
            ->group(function () {

                // Pending Workloads
                Route::get('/pending-workloads', 'pendingWorkloads')
                    ->name('pending-workloads');

                Route::get('/pending-workloads/print', 'printPendingWorkloads')
                    ->name('pending-workloads.print');

                // Missed Schedules
                Route::get('/missed-schedules', 'missedSchedules')
                    ->name('missed-schedules');

                Route::get('/missed-schedules/print', 'printMissedSchedules')
                    ->name('missed-schedules.print');
            });

        // Add inside the existing Admin route group:
        Route::prefix('consumer-feedback')
            ->name('consumer-feedback.')
            ->controller(AdminConsumerFeedbackController::class)
            ->group(function () {
                // Keep print before the dynamic {feedback} route.
                Route::get('/print', 'print')
                    ->name('print');

                Route::get('/', 'index')
                    ->name('index');

                Route::get('/{feedback}', 'show')
                    ->whereNumber('feedback')
                    ->name('show');
            });

        // Admin Consumer Management
        Route::get('/consumers/print-report', [
            AdminConsumerController::class,
            'printReport'
        ])->name('consumers.print-report');

        Route::get('/consumers', [
            AdminConsumerController::class,
            'index'
        ])->name('consumers.index');

        Route::get('/consumers/{consumer}', [
            AdminConsumerController::class,
            'show'
        ])->name('consumers.show');


        /*
|--------------------------------------------------------------------------
| Email / SMTP Settings
|--------------------------------------------------------------------------
*/

        Route::get(
            '/settings/mail',
            [MailSettingsController::class, 'index']
        )->name('settings.mail.index');

        Route::put(
            '/settings/mail',
            [MailSettingsController::class, 'update']
        )->middleware('throttle:10,1')
            ->name('settings.mail.update');

        Route::post(
            '/settings/mail/test',
            [MailSettingsController::class, 'test']
        )->middleware('throttle:5,1')
            ->name('settings.mail.test');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])
            ->name('audit-logs.export');

        Route::get('/audit-logs/print', [AuditLogController::class, 'print'])
            ->name('audit-logs.print');

        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
            ->whereNumber('auditLog')
            ->name('audit-logs.show');
    });


/*
|--------------------------------------------------------------------------
| CUSTOMER SERVICE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Customer Service'])
    ->prefix('customer-service')
    ->name('customer-service.')
    ->group(function () {


        Route::get(
            '/dashboard',
            [CustomerServiceDashboard::class, 'index']
        )->name('dashboard');


        Route::get(
            '/sidebar-notifications',
            [ConsumerFeedbackController::class, 'sidebarNotifications']
        )->name('sidebar-notifications');


        Route::get(
            '/consumers/print-report',
            [ConsumerController::class, 'printReport']
        )->name('consumers.print-report');


        Route::resource(
            'consumers',
            ConsumerController::class
        );

        Route::get(
            '/complaints/print-report',
            [CustomerServiceComplaintController::class, 'printReport']
        )->name('complaints.print-report');

        Route::resource(
            'complaints',
            CustomerServiceComplaintController::class
        );
        Route::get('/complaint-categories/print', [
            \App\Http\Controllers\CustomerService\ComplaintCategoryController::class,
            'print',
        ])->name('complaint-categories.print');

        Route::resource(
            'complaint-categories',
            ComplaintCategoryController::class
        )->except(['show']);


        Route::resource(
            'divisions',
            DivisionController::class
        )->except([
            'show',
        ]);


        Route::get(
            '/complaint-verification',
            [ComplaintVerificationController::class, 'index']
        )->name('complaint-verification.index');


        Route::post(
            '/complaints/{complaint}/verify',
            [CustomerServiceComplaintController::class, 'verify']
        )->name('complaints.verify');


        Route::post(
            '/complaints/{complaint}/reject',
            [CustomerServiceComplaintController::class, 'reject']
        )->name('complaints.reject');

        Route::post(
            '/complaints/{complaint}/commercial/start',
            [
                CustomerServiceComplaintController::class,
                'startCommercialProcessing',
            ]
        )->name('complaints.commercial.start');


        Route::put(
            '/complaints/{complaint}/commercial/resolution',
            [
                CustomerServiceComplaintController::class,
                'saveCommercialResolution',
            ]
        )->name('complaints.commercial.resolution');


        Route::post(
            '/complaints/{complaint}/commercial/complete',
            [
                CustomerServiceComplaintController::class,
                'completeCommercialComplaint',
            ]
        )->name('complaints.commercial.complete');


        Route::post(
            '/complaints/{complaint}/commercial/forward-maintenance',
            [
                CustomerServiceComplaintController::class,
                'forwardCommercialToMaintenance',
            ]
        )->name('complaints.commercial.forward-maintenance');


        Route::post(
            '/complaints/{complaint}/commercial/close',
            [
                CustomerServiceComplaintController::class,
                'closeCommercialComplaint',
            ]
        )->name('complaints.commercial.close');

        /*
|--------------------------------------------------------------------------
| Engineering / Maintenance Complaint Closure
|--------------------------------------------------------------------------
| Only Customer Service may officially close a complaint
| after the Maintenance Manager approves the accomplishment report.
*/

        Route::post(
            '/complaints/{complaint}/maintenance/close',
            [
                CustomerServiceComplaintController::class,
                'closeMaintenanceComplaint',
            ]
        )
            ->middleware('throttle:5,1')
            ->name('complaints.maintenance.close');



        Route::get(
            '/commercial-services',
            [\App\Http\Controllers\CustomerService\CommercialServicesController::class, 'index']
        )->name('commercial-services.index');

        Route::get(
            '/commercial-services/print',
            [\App\Http\Controllers\CustomerService\CommercialServicesController::class, 'print']
        )->name('commercial-services.print');

        Route::get(
            '/final-closures',
            [FinalClosureController::class, 'index']
        )->name('final-closures.index');

        Route::get('/final-closures/print', [FinalClosureController::class, 'print'])
            ->name('final-closures.print');



        Route::get(
            '/feedback',
            [ConsumerFeedbackController::class, 'index']
        )->name('feedback.index');


        Route::get('/feedback/print', [
            \App\Http\Controllers\CustomerService\ConsumerFeedbackController::class,
            'print',
        ])->name('feedback.print');


        Route::get(
            '/feedback/{feedback}',
            [ConsumerFeedbackController::class, 'show']
        )->name('feedback.show');

        Route::patch(
            '/feedback/{feedback}/review',
            [ConsumerFeedbackController::class, 'markReviewed']
        )->name('feedback.review');

        Route::patch(
            '/feedback/{feedback}/require-follow-up',
            [ConsumerFeedbackController::class, 'requireFollowUp']
        )->name('feedback.require-follow-up');

        Route::patch(
            '/feedback/{feedback}/start-follow-up',
            [ConsumerFeedbackController::class, 'startFollowUp']
        )->name('feedback.start-follow-up');

        Route::patch(
            '/feedback/{feedback}/resolve',
            [ConsumerFeedbackController::class, 'resolve']
        )->name('feedback.resolve');


        Route::get(
            '/announcements',
            [CustomerServiceAnnouncementController::class, 'index']
        )->name('announcements.index');

        Route::get('/announcements/print', [
            \App\Http\Controllers\CustomerService\ServiceAnnouncementController::class,
            'print',
        ])->name('announcements.print');

        Route::get(
            '/announcements/create',
            [CustomerServiceAnnouncementController::class, 'create']
        )->name('announcements.create');


        Route::post(
            '/announcements',
            [CustomerServiceAnnouncementController::class, 'store']
        )->name('announcements.store');

        Route::get(
            '/announcements/{serviceAnnouncement}',
            [CustomerServiceAnnouncementController::class, 'show']
        )->name('announcements.show');


        Route::get(
            '/announcements/{serviceAnnouncement}/edit',
            [CustomerServiceAnnouncementController::class, 'edit']
        )->name('announcements.edit');

        Route::put(
            '/announcements/{serviceAnnouncement}',
            [CustomerServiceAnnouncementController::class, 'update']
        )->name('announcements.update');

        Route::patch(
            '/announcements/{serviceAnnouncement}/publish',
            [CustomerServiceAnnouncementController::class, 'publish']
        )->name('announcements.publish');

        Route::patch(
            '/announcements/{serviceAnnouncement}/archive',
            [CustomerServiceAnnouncementController::class, 'archive']
        )->name('announcements.archive');

        Route::patch(
            '/announcements/{serviceAnnouncement}/draft',
            [CustomerServiceAnnouncementController::class, 'moveToDraft']
        )->name('announcements.draft');

        Route::delete(
            '/announcements/{serviceAnnouncement}',
            [CustomerServiceAnnouncementController::class, 'destroy']
        )->name('announcements.destroy');

        Route::get(
            '/reports',
            [ReportsAnalyticsController::class, 'index']
        )->name('reports.index');

        Route::get(
            '/reports/print',
            [ReportsAnalyticsController::class, 'print']
        )->name('reports.print');

        Route::get(
            '/pending-workloads',
            [MaintenanceMonitoringController::class, 'pendingWorkloads']
        )->name('pending-workloads.index');

        Route::get(
            '/pending-workloads/print',
            [MaintenanceMonitoringController::class, 'printPendingWorkloads']
        )->name('pending-workloads.print');


        Route::get(
            '/missed-schedules',
            [MaintenanceMonitoringController::class, 'missedSchedules']
        )->name('missed-schedules.index');

        Route::get(
            '/missed-schedules/print',
            [MaintenanceMonitoringController::class, 'printMissedSchedules']
        )->name('missed-schedules.print');
    });


/*
|--------------------------------------------------------------------------
| MAINTENANCE MANAGER / SUPERVISOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Maintenance Manager'])
    ->prefix('maintenance-manager')
    ->name('maintenance-manager.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [ManagerDashboard::class, 'index']
        )->name('dashboard');

        Route::get('/insights', [\App\Http\Controllers\Manager\MaintenanceInsightsController::class, 'index'])
            ->name('insights.index');
        Route::get('/insights/print', [\App\Http\Controllers\Manager\MaintenanceInsightsController::class, 'print'])
            ->name('insights.print');

        /*
|--------------------------------------------------------------------------
| Plumber Service Areas
|--------------------------------------------------------------------------
*/
        /*
|--------------------------------------------------------------------------
| Daily Maintenance Scheduling
|--------------------------------------------------------------------------
*/

        Route::get(
            '/schedules',
            [MaintenanceScheduleController::class, 'index']
        )->name('schedules.index');


        Route::get(
            '/pending-workloads',
            [\App\Http\Controllers\Manager\PendingWorkloadController::class, 'index']
        )->name('pending-workloads.index');


        Route::get(
            '/backlogs',
            [\App\Http\Controllers\Manager\BacklogController::class, 'index']
        )->name('backlogs.index');

        Route::get(
            '/backlogs/{workOrder}/reschedule',
            [\App\Http\Controllers\Manager\MissedScheduleRescheduleController::class, 'edit']
        )->name('backlogs.reschedule.edit');

        Route::post(
            '/backlogs/{workOrder}/reschedule',
            [\App\Http\Controllers\Manager\MissedScheduleRescheduleController::class, 'store']
        )->middleware('throttle:10,1')->name('backlogs.reschedule.store');



        Route::post(
            '/schedules/work-orders',
            [MaintenanceScheduleController::class, 'store']
        )->name('schedules.store');

        Route::post(
            '/schedules/{schedule}/finalize',
            [MaintenanceScheduleController::class, 'finalize']
        )->name('schedules.finalize');

        Route::post(
            '/schedules/{schedule}/finalize-additions',
            [MaintenanceScheduleController::class, 'finalizeAdditions']
        )->name('schedules.finalize-additions');

        // Audited replacement on an Approved, Finalized, unstarted work order.
        Route::patch(
            '/schedules/work-orders/{workOrder}/replace-plumber',
            [MaintenanceScheduleController::class, 'replacePlumber']
        )->name('schedules.replace-plumber');

        Route::get(
            '/schedules/work-orders/{workOrder}/handover',
            [\App\Http\Controllers\Manager\MaintenanceHandoverController::class, 'edit']
        )->name('schedules.handover.edit');

        Route::patch(
            '/schedules/work-orders/{workOrder}/handover',
            [\App\Http\Controllers\Manager\MaintenanceHandoverController::class, 'update']
        )->name('schedules.handover.update');

        Route::patch(
            '/schedules/work-orders/{workOrder}/reschedule',
            [MaintenanceScheduleController::class, 'reschedule']
        )->name('schedules.reschedule');


        Route::post(
            '/schedules/pending-workloads/{workOrder}/follow-up',
            [\App\Http\Controllers\Manager\PendingWorkloadFollowUpController::class, 'store']
        )->name('schedules.pending-follow-up');


        Route::get(
            '/service-areas',
            [ServiceAreaController::class, 'index']
        )->name('service-areas.index');

        Route::post(
            '/service-areas',
            [ServiceAreaController::class, 'store']
        )->name('service-areas.store');

        Route::put(
            '/service-areas/{serviceArea}',
            [ServiceAreaController::class, 'update']
        )->name('service-areas.update');

        Route::patch(
            '/service-areas/{serviceArea}/toggle-status',
            [ServiceAreaController::class, 'toggleStatus']
        )->name('service-areas.toggle-status');

        Route::patch(
            '/service-areas/plumbers/{plumber}/assign',
            [ServiceAreaController::class, 'assignPlumber']
        )->name('service-areas.assign-plumber');

        Route::get(
            '/complaints',
            [ManagerComplaintController::class, 'index']
        )->name('complaints.index');

        Route::get(
            '/complaints/for-assignment',
            fn() => redirect()->route('maintenance-manager.schedules.index')
        )->name('complaints.for-assignment');

        Route::get(
            '/complaints-report',
            [ManagerComplaintController::class, 'printReport']
        )->name('complaints.print-report');



        Route::get(
            '/complaints/{complaint}/report',
            [ManagerComplaintController::class, 'report']
        )->name('complaints.report');


        Route::get(
            '/complaints/{complaint}',
            [ManagerComplaintController::class, 'show']
        )->name('complaints.show');

        Route::post(
            '/complaints/{complaint}/assign',
            [ManagerComplaintController::class, 'assign']
        )->name('complaints.assign');


        /*
        |--------------------------------------------------------------------------
        | Maintenance Report Review
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/maintenance-reviews',
            [MaintenanceReviewController::class, 'index']
        )->name('maintenance-reviews.index');

        // Static print route MUST precede the dynamic {maintenanceReport} route.
        Route::get(
            '/maintenance-reviews/print',
            [MaintenanceReviewController::class, 'printReport']
        )->name('maintenance-reviews.print');

        Route::get(
            '/maintenance-reviews/{maintenanceReport}',
            [MaintenanceReviewController::class, 'show']
        )->name('maintenance-reviews.show');

        Route::post(
            '/maintenance-reviews/{maintenanceReport}/approve',
            [MaintenanceReviewController::class, 'approve']
        )->name('maintenance-reviews.approve');

        Route::post(
            '/maintenance-reviews/{maintenanceReport}/return',
            [MaintenanceReviewController::class, 'returnForCorrection']
        )->name('maintenance-reviews.return');




        /*
|--------------------------------------------------------------------------
| Technician Duty Management
|--------------------------------------------------------------------------
*/

        Route::get(
            '/technician-duties',
            [\App\Http\Controllers\Manager\TechnicianDutyController::class, 'index']
        )->name('technician-duties.index');

        Route::post(
            '/technician-duties/generate',
            [\App\Http\Controllers\Manager\TechnicianDutyController::class, 'generate']
        )->name('technician-duties.generate');

        Route::post(
            '/technician-duties',
            [\App\Http\Controllers\Manager\TechnicianDutyController::class, 'store']
        )->name('technician-duties.store');

        Route::patch(
            '/technician-duties/{duty}',
            [\App\Http\Controllers\Manager\TechnicianDutyController::class, 'update']
        )->name('technician-duties.update');

        Route::post(
            '/schedules/batch',
            [
                \App\Http\Controllers\Manager\MaintenanceScheduleController::class,
                'batchStore',
            ]
        )->name('schedules.batch-store');


        Route::get('/sidebar-counts', ManagerSidebarCountController::class)
            ->middleware('throttle:120,1')
            ->name('sidebar-counts'); // maintenance-manager.sidebar-counts

    });


/*
|--------------------------------------------------------------------------
| MAINTENANCE TECHNICIAN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Maintenance Technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {


        Route::get(
            '/dashboard',
            [TechnicianDashboard::class, 'index']
        )->name('dashboard');

        Route::get('/sidebar-counts', TechnicianSidebarCountController::class)
            ->middleware('throttle:120,1')
            ->name('sidebar-counts'); // technician.sidebar-counts


        Route::get(
            '/active-work-orders',
            [TechnicianComplaintController::class, 'activeWorkOrders']
        )->name('active-work-orders.index');

        Route::get(
            '/complaints',
            [TechnicianComplaintController::class, 'index']
        )->name('complaints.index');



        Route::get(
            '/complaints/print-report',
            [TechnicianComplaintController::class, 'printReport']
        )->name('complaints.print-report');

        Route::get(
            '/complaints/{complaint}',
            [TechnicianComplaintController::class, 'show']
        )->name('complaints.show');



        // Individual accomplishment reports submitted by the logged-in technician.
        Route::get(
            '/maintenance-reports/history/print',
            [MaintenanceReportController::class, 'printHistory']
        )->name('maintenance-reports.history.print');

        Route::get(
            '/maintenance-reports/history',
            [MaintenanceReportController::class, 'history']
        )->name('maintenance-reports.history');

        Route::get(
            '/maintenance-reports',
            [MaintenanceReportController::class, 'index']
        )->name('maintenance-reports.index');

        Route::get(
            '/complaints/{complaint}/maintenance-report',
            [MaintenanceReportController::class, 'create']
        )->name('maintenance-reports.create');

        Route::post(
            '/complaints/{complaint}/maintenance-report/start',
            [MaintenanceReportController::class, 'start']
        )->name('maintenance-reports.start');

        Route::post(
            '/complaints/{complaint}/work-orders/{workOrder}/start',
            [MaintenanceReportController::class, 'startWorkOrder']
        )->name('work-orders.start');

        /*
|--------------------------------------------------------------------------
| Record Pending Workload
|--------------------------------------------------------------------------
|
| Technician records unfinished maintenance with a required reason.
| Manager will review it for next-working-day planning.
|
*/

        Route::post(
            '/complaints/{complaint}/work-orders/{workOrder}/pending',
            [MaintenanceReportController::class, 'markPendingWorkload']
        )->name('work-orders.pending');

        Route::post(
            '/complaints/{complaint}/work-orders/{workOrder}/resume',
            [MaintenanceReportController::class, 'resumeWorkOrder']
        )->name('work-orders.resume');


        Route::post(
            '/complaints/{complaint}/maintenance-report',
            [MaintenanceReportController::class, 'store']
        )->name('maintenance-reports.store');

        Route::get(
            '/complaints/{complaint}/maintenance-report/view',
            [MaintenanceReportController::class, 'show']
        )->name('maintenance-reports.show');

        Route::get(
            '/complaints/{complaint}/maintenance-report/print',
            [MaintenanceReportController::class, 'printReport']
        )->name('maintenance-reports.print');

        Route::get(
            '/reports/maintenance',
            [MaintenanceReportController::class, 'index']
        )->name('reports.maintenance');

        Route::get(
            '/reports/maintenance/print',
            [MaintenanceReportController::class, 'print']
        )->name('reports.maintenance.print');

        Route::get(
            '/schedule/print',
            [\App\Http\Controllers\Technician\ScheduleController::class, 'print']
        )->name('schedule.print');

        Route::get(
            '/schedule',
            [\App\Http\Controllers\Technician\ScheduleController::class, 'index']
        )->name('schedule.index');

        Route::post(
            '/schedule/missed/{workOrder}/report',
            [\App\Http\Controllers\Technician\ScheduleController::class, 'reportMissed']
        )->middleware('throttle:5,1')->name('schedule.missed.report');

        Route::get(
            '/pending-workload',
            [\App\Http\Controllers\Technician\PendingWorkloadController::class, 'index']
        )->name('pending-workload.index');

        Route::get(
            '/pending-workload/print',
            [\App\Http\Controllers\Technician\PendingWorkloadController::class, 'print']
        )
            ->name('pending-workload.print');
    });




Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/consumer/login',
        [ConsumerLoginController::class, 'create']
    )->name('consumer.login');


    Route::post(
        '/consumer/login',
        [ConsumerLoginController::class, 'store']
    )->name('consumer.login.store');


    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| Forgot Password
|--------------------------------------------------------------------------
*/

    Route::get(
        '/consumer/forgot-password',
        [ForgotPasswordController::class, 'create']
    )->name('consumer.password.request');

    Route::post(
        '/consumer/forgot-password',
        [ForgotPasswordController::class, 'store']
    )
        ->middleware('throttle:10,1')
        ->name('consumer.password.email');


    /*
|--------------------------------------------------------------------------
| Password Reset OTP Verification
|--------------------------------------------------------------------------
*/

    Route::get(
        '/consumer/forgot-password/verify',
        [ForgotPasswordController::class, 'showVerifyOtp']
    )->name('consumer.password.verify');

    Route::post(
        '/consumer/forgot-password/verify',
        [ForgotPasswordController::class, 'verifyOtp']
    )
        ->middleware('throttle:10,1')
        ->name('consumer.password.verify.submit');


    /*
|--------------------------------------------------------------------------
| Resend OTP
|--------------------------------------------------------------------------
*/

    Route::post(
        '/consumer/forgot-password/resend',
        [ForgotPasswordController::class, 'resendOtp']
    )
        ->middleware('throttle:10,1')
        ->name('consumer.password.resend');


    /*
|--------------------------------------------------------------------------
| Create New Password
|--------------------------------------------------------------------------
*/

    Route::get(
        '/consumer/reset-password',
        [ResetPasswordController::class, 'create']
    )->name('consumer.password.reset');

    Route::post(
        '/consumer/reset-password',
        [ResetPasswordController::class, 'store']
    )
        ->middleware('throttle:5,1')
        ->name('consumer.password.update');



    /*
    |--------------------------------------------------------------------------
    | Consumer Registration
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/consumer/register',
        [ConsumerRegisterController::class, 'create']
    )->name('consumer.register');


    Route::get(
        '/consumer/register/verify-email',
        [VerifyEmailController::class, 'show']
    )->name('consumer.register.verify-email');


    Route::post(
        '/consumer/register/verify-email',
        [VerifyEmailController::class, 'verify']
    )
        ->middleware('throttle:10,1')
        ->name('consumer.register.verify-email.submit');


    Route::post(
        '/consumer/register/verify-email/resend',
        [VerifyEmailController::class, 'resend']
    )
        ->middleware('throttle:3,1')
        ->name('consumer.register.verify-email.resend');


    Route::post(
        '/consumer/register/verify-email/change-email',
        [VerifyEmailController::class, 'changeEmail']
    )
        ->middleware('throttle:3,1')
        ->name(
            'consumer.register.verify-email.change-email'
        );


    Route::post(
        '/consumer/register',
        [ConsumerRegisterController::class, 'store']
    )->name('consumer.register.store');
});


/*
|--------------------------------------------------------------------------
| CONSUMER PORTAL
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| CONSUMER REGISTRATION STATUS
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:Consumer',
])
    ->prefix('consumer')
    ->name('consumer.')
    ->group(function () {

        Route::get(
            '/registration-status',
            [RegistrationStatusController::class, 'show']
        )->name('registration.status');

        Route::get(
            '/registration/correct',
            [RegistrationStatusController::class, 'edit']
        )->name('registration.edit');

        Route::put(
            '/registration/resubmit',
            [RegistrationStatusController::class, 'update']
        )->name('registration.update');
    });


/*
|--------------------------------------------------------------------------
| VERIFIED CONSUMER PORTAL
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:Consumer',
    'consumer.verified',
])
    ->prefix('consumer')
    ->name('consumer.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [ConsumerDashboardController::class, 'index']
        )->name('dashboard');


        Route::get(
            '/complaints',
            [ConsumerComplaintController::class, 'index']
        )->name('complaints.index');

        Route::get(
            '/complaints/refresh-state',
            [ConsumerComplaintController::class, 'refreshIndexState']
        )->name('complaints.refresh-state');

        Route::get(
            '/complaints/{complaint}/refresh-state',
            [ConsumerComplaintController::class, 'refreshShowState']
        )->name('complaints.show.refresh-state');


        Route::get(
            '/complaints/create',
            [ConsumerComplaintController::class, 'create']
        )->name('complaints.create');


        Route::post(
            '/complaints',
            [ConsumerComplaintController::class, 'store']
        )->name('complaints.store');


        Route::get(
            '/complaints/{complaint}/edit',
            [ConsumerComplaintController::class, 'edit']
        )->name('complaints.edit');


        Route::put(
            '/complaints/{complaint}',
            [ConsumerComplaintController::class, 'update']
        )->name('complaints.update');

        Route::post(
            '/complaints/analyze',
            [ConsumerComplaintController::class, 'analyze']
        )->name('complaints.analyze');


        Route::get(
            '/complaints/{complaint}',
            [ConsumerComplaintController::class, 'show']
        )->name('complaints.show');


        Route::get(
            '/announcements',
            [ServiceAnnouncementController::class, 'index']
        )->name('announcements.index');

        Route::post(
            '/announcements/mark-all-read',
            [ServiceAnnouncementController::class, 'markAllAsRead']
        )->name('announcements.mark-all-read');


        Route::get(
            '/announcements/{serviceAnnouncement}',
            [ServiceAnnouncementController::class, 'show']
        )->name('announcements.show');


        Route::get(
            '/ai-assistant',
            [AIController::class, 'index']
        )->name('ai.index');


        Route::post(
            '/ai-assistant/ask',
            [AIController::class, 'ask']
        )->name('ai.ask');

        Route::get(
            '/complaints/{complaint}/feedback',
            [ComplaintFeedbackController::class, 'create']
        )->name('complaints.feedback.create');

        Route::post(
            '/complaints/{complaint}/feedback',
            [ComplaintFeedbackController::class, 'store']
        )->name('complaints.feedback.store');


        Route::get('/profile', [ConsumerProfileController::class, 'show'])
            ->name('profile.show');

        Route::get('/profile/edit', [ConsumerProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/profile', [ConsumerProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [ConsumerProfileController::class, 'updatePassword'])
            ->name('profile.password');
    });

require __DIR__ . '/auth.php';
