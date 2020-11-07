<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EmployeePayRate
 *
 * @property int $id
 * @property int $employee_id
 * @property int $pay_rate_id
 * @property string|null $from
 * @property string|null $to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|EmployeePayRate newModelQuery()
 * @method static Builder|EmployeePayRate newQuery()
 * @method static Builder|EmployeePayRate query()
 * @method static Builder|EmployeePayRate whereCreatedAt($value)
 * @method static Builder|EmployeePayRate whereEmployeeId($value)
 * @method static Builder|EmployeePayRate whereFrom($value)
 * @method static Builder|EmployeePayRate whereId($value)
 * @method static Builder|EmployeePayRate wherePayRateId($value)
 * @method static Builder|EmployeePayRate whereTo($value)
 * @method static Builder|EmployeePayRate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class EmployeePayRate extends Model
{
    protected $table = 'employee_pay_rate';

    use HasFactory;
}
