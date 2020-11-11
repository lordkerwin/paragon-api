<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShiftStatus
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Shift[] $shifts
 * @property-read int|null $shifts_count
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShiftStatus extends Model
{
    use HasFactory;

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public static function retrieve($name, $field = null)
    {
        $record = ShiftStatus::where('name', $name)->firstOrFail();

        if (!is_null($field)) {
            return $record->{$field};
        }

        return $record;
    }

}
