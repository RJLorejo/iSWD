<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [

        'department_code',

        'department_name',

        'description',

        'is_active',

    ];

    public static function generateCode(string $name): string
    {
        $name = trim($name);

        $words = preg_split('/\s+/', $name);

        if (count($words) > 1) {

            $code = collect($words)
                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                ->implode('');
        } else {

            $code = strtoupper(substr($name, 0, 5));
        }

        $original = $code;
        $counter = 2;

        while (self::where('department_code', $code)->exists()) {

            $code = $original . $counter;
            $counter++;
        }

        return $code;
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($department) {

            if (empty($department->department_code)) {

                $lastId = self::max('id') + 1;

                $department->department_code = 'DEP-' .
                    str_pad($lastId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
