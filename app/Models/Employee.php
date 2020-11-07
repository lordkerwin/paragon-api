<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string|null $profile_image_url
 * @property int|null $role_id
 * @property int|null $department_id
 * @property int|null $employee_type_id
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Department|null $department
 * @property-read EmployeeType|null $employeeType
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @property-read Collection|PayRate[] $payRates
 * @property-read int|null $pay_rates_count
 * @property-read Role|null $role
 * @method static Builder|Employee newModelQuery()
 * @method static Builder|Employee newQuery()
 * @method static \Illuminate\Database\Query\Builder|Employee onlyTrashed()
 * @method static Builder|Employee query()
 * @method static Builder|Employee whereActive($value)
 * @method static Builder|Employee whereCreatedAt($value)
 * @method static Builder|Employee whereDeletedAt($value)
 * @method static Builder|Employee whereDepartmentId($value)
 * @method static Builder|Employee whereEmployeeTypeId($value)
 * @method static Builder|Employee whereId($value)
 * @method static Builder|Employee whereKey($value)
 * @method static Builder|Employee whereName($value)
 * @method static Builder|Employee whereProfileImageUrl($value)
 * @method static Builder|Employee whereRoleId($value)
 * @method static Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Employee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Employee withoutTrashed()
 * @mixin Eloquent
 */
class Employee extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->key = uniqid();
        });
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function payRates()
    {
        return $this->belongsToMany(PayRate::class, 'employee_pay_rate')
            ->withPivot('from', 'to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getCurrentPayRate($date = null)
    {
        if (empty($date)) {
            $date = Carbon::now();
        }

        return $this->payRates()
            ->where(function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->where('employee_pay_rate.from', '<=', $date);
                    $q->where('employee_pay_rate.to', null);
                });
                $query->orWhere(function ($q) use ($date) {
                    $q->where('employee_pay_rate.from', '<=', $date);
                    $q->where('employee_pay_rate.to', '>', $date);
                });
            })
            ->first();
    }

    public function getLastEvent()
    {
        return $this->events()->orderByDesc('created_at')->first();
    }

    public function getAvailableEvents()
    {
        $last_event = $this->getLastEvent();

        if (!$last_event) {
            return EventType::where('name', 'clock-in')->get();
        }

        switch ($last_event->event_type_id) {
            case EventType::retrieve('clock-in', 'id'):
                // last event was clock in, so available events are, 'off-site' or 'clock-out'
                return EventType::where('name', 'clock-out')->where('name', 'off-site')->get();
            case EventType::retrieve('clock-out', 'id'):
                // last event was clock out, so available events are, 'clock-in'
                return EventType::where('name', 'clock-in')->get();
            case EventType::retrieve('off-site', 'id'):
                // last event was off-site, so available events are, 'on-site' or 'clock-out'
                return EventType::where('name', 'clock-out')->where('name', 'on-site')->get();
            case EventType::retrieve('on-site', 'id'):
                // last event was on-site, so available events are, 'off-site' or 'clock-out'
                return EventType::where('name', 'clock-out')->where('name', 'off-site')->get();
        }
    }

}
