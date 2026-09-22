<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Division;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ComplaintCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'division_id',
        'code',
        'name',
        'description',
        'category_type',
        'requires_maintenance',
        'is_active',

    ];


    protected $casts = [

        'is_active' => 'boolean',

        'requires_maintenance' => 'boolean',

    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }



    /*
    |--------------------------------------------------------------------------
    | Generate Category Code
    |--------------------------------------------------------------------------
    */

    public static function generateCode(string $name): string
    {
        $name = trim($name);

        $words = preg_split('/\s+/', $name);

        if (count($words) > 1) {

            $code = collect($words)
                ->map(
                    fn($word) =>
                    strtoupper(substr($word, 0, 1))
                )
                ->implode('');
        } else {

            $code = strtoupper(
                substr($name, 0, 5)
            );
        }


        $original = $code;

        $counter = 2;


        while (
            self::withTrashed()
            ->where('code', $code)
            ->exists()
        ) {

            $code = $original . $counter;

            $counter++;
        }


        return $code;
    }
}
