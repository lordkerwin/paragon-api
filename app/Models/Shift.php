<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Shift
 *
 * @property int $id
 * @property int $employee_id
 * @property string $start
 * @property string $end
 * @property string $pay_rate
 * @property string $duration
 * @property string $value
 * @property int $shift_status_id
 * @property int $processed
 * @property int $payroll_locked
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShiftItem[] $shiftItems
 * @property-read int|null $shift_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift wherePayRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift wherePayrollLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereShiftStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereValue($value)
 * @mixin \Eloquent
 */
class Shift extends Model
{
    use HasFactory;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shiftItems()
    {
        return $this->hasMany(ShiftItem::class);
    }

}
