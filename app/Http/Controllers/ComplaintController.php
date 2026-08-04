<?php

namespace App\Http\Controllers\Complaint;

use App\Http\Controllers\Controller;

use App\Repositories\ComplaintRepository;

class ComplaintController extends Controller
{
    protected $complaints;

    public function __construct(
        ComplaintRepository $complaints
    ) {
        $this->complaints = $complaints;
    }

    public function index()
    {
        return view(

            'complaints.index',

            [

                'complaints' => $this

                    ->complaints

                    ->paginate()

            ]

        );
    }
}
