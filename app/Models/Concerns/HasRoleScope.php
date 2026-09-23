<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Transforma uma subclasse de User em uma "entidade" restrita a uma role do Spatie.
 *
 * - Filtra as consultas pela role retornada em roleName().
 * - Atribui essa role automaticamente ao criar o registro.
 * - Mantém o morph class como User, para que roles e relações polimórficas
 *   sejam compartilhadas entre User e a subclasse.
 */
trait HasRoleScope
{
    abstract public static function roleName(): string;

    public static function bootHasRoleScope(): void
    {
        static::addGlobalScope('role', fn (Builder $query) => $query->role(static::roleName()));
        static::created(fn (self $model) => $model->assignRole(static::roleName()));
    }

    public function getTable(): string
    {
        return 'users';
    }

    public function getMorphClass(): string
    {
        return User::class;
    }

    public function guardName(): string
    {
        return 'web';
    }
}
