<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(range(7, 23))->each(
            fn (int $hour) => Schedule::query()->firstOrCreate([
                'starts_at' => sprintf('%02d:00', $hour),
            ])
        );
    }
}
