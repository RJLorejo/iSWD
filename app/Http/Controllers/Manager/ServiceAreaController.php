<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ServiceArea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceAreaController extends Controller
{
    public function index()
    {
        $serviceAreas = ServiceArea::query()
            ->withCount([
                'plumbers' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->whereHas('roles', function ($roleQuery) {
                            $roleQuery->where(
                                'name',
                                'Maintenance Technician'
                            );
                        });
                },
            ])
            ->orderBy('name')
            ->get();

        $plumbers = User::query()
            ->role('Maintenance Technician')
            ->where('is_active', true)
            ->with([
                'position',
                'serviceArea',
            ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'maintenance-manager.service-areas.index',
            compact(
                'serviceAreas',
                'plumbers'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:service_areas,name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        ServiceArea::create([
            'name' => trim($validated['name']),

            'description' =>
            $validated['description'] ?? null,

            'is_active' => true,
        ]);

        return redirect()
            ->route(
                'maintenance-manager.service-areas.index'
            )
            ->with(
                'success',
                'Service area created successfully.'
            );
    }

    public function update(
        Request $request,
        ServiceArea $serviceArea
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'service_areas',
                    'name'
                )->ignore($serviceArea->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $serviceArea->update([
            'name' => trim($validated['name']),

            'description' =>
            $validated['description'] ?? null,
        ]);

        return redirect()
            ->route(
                'maintenance-manager.service-areas.index'
            )
            ->with(
                'success',
                'Service area updated successfully.'
            );
    }

    public function toggleStatus(
        ServiceArea $serviceArea
    ) {
        $serviceArea->update([
            'is_active' => !$serviceArea->is_active,
        ]);

        $message = $serviceArea->is_active
            ? 'Service area activated successfully.'
            : 'Service area deactivated successfully.';

        return redirect()
            ->route(
                'maintenance-manager.service-areas.index'
            )
            ->with(
                'success',
                $message
            );
    }

    public function assignPlumber(
        Request $request,
        User $plumber
    ) {
        abort_unless(
            $plumber->hasRole(
                'Maintenance Technician'
            ),
            404
        );

        $validated = $request->validate([
            'service_area_id' => [
                'nullable',

                Rule::exists(
                    'service_areas',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                }),
            ],
        ]);

        $plumber->update([
            'service_area_id' =>
            $validated['service_area_id']
                ?? null,
        ]);

        $message =
            empty($validated['service_area_id'])
            ? 'Plumber service area removed successfully.'
            : 'Plumber service area assigned successfully.';

        return redirect()
            ->route(
                'maintenance-manager.service-areas.index'
            )
            ->with(
                'success',
                $message
            );
    }
}
