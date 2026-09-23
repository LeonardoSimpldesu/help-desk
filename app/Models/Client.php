<?php

namespace App\Models;

use App\Models\Concerns\HasRoleScope;

class Client extends User
{
    use HasRoleScope;

    public static function roleName(): string
    {
        return 'client';
    }
}
