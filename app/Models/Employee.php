<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

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
