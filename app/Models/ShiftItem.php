<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShiftItem
 *
 * @property int $id
 * @property int $shift_id
 * @property string $start
 * @property string $end
 * @property string $duration
 * @property string $value
 * @property int $shift_item_type_id
 * @property int $processed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Shift $shift
 * @property-read \App\Models\ShiftItemType $type
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereShiftItemTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItem whereValue($value)
 * @mixin \Eloquent
 */
class ShiftItem extends Model
{
    use HasFactory;

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function type()
    {
        return $this->belongsTo(ShiftItemType::class);
    }

}
