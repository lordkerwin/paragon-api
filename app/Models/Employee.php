<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return $this->belongsToMany(PayRate::class, 'employee_pay_rate')->withPivot('from', 'to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getCurrentPayRate()
    {
        $pay_rate = $this->payRates()
            ->wherePivot('from', '<=', Carbon::now())
            ->wherePivot('to', '>', Carbon::now())
            ->orWhere(function ($query) {
                $query->wherePivot('from', '<=', Carbon::now())
                    ->wherePivot('to', null);
            })
            ->get();
        return $pay_rate;
    }
}
