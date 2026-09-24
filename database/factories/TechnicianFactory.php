<?php

namespace Database\Factories;

use App\Models\Technician;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends UserFactory
 *
 * @method Technician create($attributes = [], ?Model $parent = null)
 * @method Technician make($attributes = [], ?Model $parent = null)
 */
class TechnicianFactory extends UserFactory
{
    protected $model = Technician::class;
}
