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
}
