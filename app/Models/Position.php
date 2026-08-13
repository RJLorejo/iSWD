<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'position_code',
        'position_name',
        'department_id',
        'description',
        'is_active',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

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

        while (self::where('position_code', $code)->exists()) {

            $code = $original . $counter;
            $counter++;
        }

        return $code;
    }
}
