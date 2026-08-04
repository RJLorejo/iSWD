<?php

namespace App\Services;

use App\Models\Complaint;

class ComplaintNumberService
{
    public function generate()
    {
        $year = date('Y');

        $count = Complaint::count() + 1;

        return sprintf(
            'SWD-%s-%06d',
            $year,
            $count
        );
    }
}
