<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\PayRate
 *
 * @property int $id
 * @property string $rate
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PayRate newModelQuery()
 * @method static Builder|PayRate newQuery()
 * @method static \Illuminate\Database\Query\Builder|PayRate onlyTrashed()
 * @method static Builder|PayRate query()
 * @method static Builder|PayRate whereCreatedAt($value)
 * @method static Builder|PayRate whereDeletedAt($value)
 * @method static Builder|PayRate whereId($value)
 * @method static Builder|PayRate whereRate($value)
 * @method static Builder|PayRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PayRate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PayRate withoutTrashed()
 * @mixin Eloquent
 */
class PayRate extends Model
{
    use HasFactory, SoftDeletes;
}
