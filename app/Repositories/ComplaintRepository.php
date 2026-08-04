<?php

namespace App\Repositories;

use App\Models\Complaint;

class ComplaintRepository
{
    public function paginate()
    {
        return Complaint::latest()->paginate(10);
    }

    public function find($id)
    {
        return Complaint::findOrFail($id);
    }

    public function create(array $data)
    {
        return Complaint::create($data);
    }
}
