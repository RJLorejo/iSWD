<?php

namespace App\Services;

use App\Models\Complaint;

class ComplaintService
{
    public function create(array $data)
    {
        return Complaint::create($data);
    }

    public function updateStatus(
        Complaint $complaint,
        string $status
    ) {
        $complaint->update([

            'status' => $status

        ]);
    }
}
