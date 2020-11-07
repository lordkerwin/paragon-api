<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property int $event_type_id
 * @property int $employee_id
 * @property int $processed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Employee $employee
 * @property-read EventType $eventType
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereEmployeeId($value)
 * @method static Builder|Event whereEventTypeId($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereProcessed($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Event extends Model
{
    use HasFactory;

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
