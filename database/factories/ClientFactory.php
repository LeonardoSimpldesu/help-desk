<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends UserFactory
 *
 * @method Client create($attributes = [], ?Model $parent = null)
 * @method Client make($attributes = [], ?Model $parent = null)
 */
class ClientFactory extends UserFactory
{
    protected $model = Client::class;
}
