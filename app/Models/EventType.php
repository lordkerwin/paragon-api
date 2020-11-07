<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EventType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @method static Builder|EventType newModelQuery()
 * @method static Builder|EventType newQuery()
 * @method static Builder|EventType query()
 * @method static Builder|EventType whereCreatedAt($value)
 * @method static Builder|EventType whereId($value)
 * @method static Builder|EventType whereName($value)
 * @method static Builder|EventType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class EventType extends Model
{
    use HasFactory;


    public function events()
    {
        return $this->hasMany(Event::class);
    }


    public static function retrieve($name, $field = null)
    {
        $record = EventType::where('name', $name)->firstOrFail();

        if (!is_null($field)) {
            return $record->{$field};
        }

        return $record;
    }

}
