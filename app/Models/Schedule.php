<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model
{
    protected $fillable = [
        'starts_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime:H:i',
        ];
    }

    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(Technician::class, 'schedule_technician');
    }

    protected function label(): Attribute
    {
        return Attribute::get(fn () => $this->starts_at->format('H:i'));
    }

    protected function period(): Attribute
    {
        return Attribute::get(fn (): string => match (true) {
            $this->starts_at->hour <= 12 => 'Manhã',
            $this->starts_at->hour <= 18 => 'Tarde',
            default => 'Noite',
        });
    }
}
