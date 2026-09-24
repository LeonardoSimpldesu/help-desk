<?php

namespace App\Models;

use App\Models\Concerns\HasRoleScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technician extends User
{
    use HasRoleScope;

    public static function roleName(): string
    {
        return 'technician';
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'schedule_technician');
    }
}
