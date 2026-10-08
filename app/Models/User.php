<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    protected $fillable = [

        'employee_id',

        'first_name',

        'middle_name',

        'last_name',

        'suffix',

        'email',

        'email_verified_at',

        'password',

        'phone',

        'avatar',

        'department_id',

        'position_id',

        'service_area_id',

        'is_active',

        'last_login_at',

        'last_login_ip',



    ];

    protected $hidden = [

        'password',

        'remember_token',

    ];

    protected $casts = [

        'email_verified_at' => 'datetime',

        'last_login_at' => 'datetime',

        'password' => 'hashed',

        'is_active' => 'boolean',

    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function serviceArea()
    {
        return $this->belongsTo(
            ServiceArea::class,
            'service_area_id'
        );
    }

    public function consumer()
    {
        return $this->hasOne(Consumer::class);
    }

    public function assignedComplaints()
    {
        return $this->belongsToMany(
            Complaint::class,
            'complaint_technicians',
            'technician_id',
            'complaint_id'
        )->withPivot([
            'assignment_role',
            'status',
            'assigned_at',
            'started_at',
            'completed_at',
        ])->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ])->filter()->implode(' ');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {

            return asset(
                'storage/' . $this->avatar
            );
        }

        return 'https://ui-avatars.com/api/?background=0ea5e9&color=ffffff&name='
            . urlencode($this->full_name);
    }
}
