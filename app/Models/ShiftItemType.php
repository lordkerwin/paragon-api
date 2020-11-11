<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShiftItemType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShiftItem[] $shiftItems
 * @property-read int|null $shift_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShiftItemType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShiftItemType extends Model
{
    use HasFactory;

    public function shiftItems()
    {
        return $this->hasMany(ShiftItem::class);
    }
}
