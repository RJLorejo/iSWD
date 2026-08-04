<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->q);

        $users = collect();
        $departments = collect();
        $positions = collect();

        if ($keyword != '') {

            $users = User::with(['department', 'position', 'roles'])

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


            $departments = Department::where('department_name', 'LIKE', "%{$keyword}%")

                ->limit(10)

                ->get();


            $positions = Position::where('position_name', 'LIKE', "%{$keyword}%")

                ->limit(10)

                ->get();
        }

        return view('search.index', compact(

            'keyword',

            'users',

            'departments',

            'positions'

        ));
    }
}
