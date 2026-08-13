<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaintCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'code',

        'name',

        'description',

        'is_active',

    ];

    protected $casts = [

        'is_active' => 'boolean',

    ];


    public function complaints()
    {
        return $this->hasMany(Complaint::class);
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

        while (self::withTrashed()->where('code', $code)->exists()) {

            $code = $original . $counter;

            $counter++;
        }

        return $code;
    }
}
