<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EmployeeType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|EmployeeType newModelQuery()
 * @method static Builder|EmployeeType newQuery()
 * @method static Builder|EmployeeType query()
 * @method static Builder|EmployeeType whereCreatedAt($value)
 * @method static Builder|EmployeeType whereId($value)
 * @method static Builder|EmployeeType whereName($value)
 * @method static Builder|EmployeeType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class EmployeeType extends Model
{
    use HasFactory;
}
