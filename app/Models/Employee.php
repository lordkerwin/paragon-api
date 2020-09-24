<?php

namespace App\Models;

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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
