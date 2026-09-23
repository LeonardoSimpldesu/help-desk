<?php

use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests run on the Laravel TestCase with a lazily refreshed database,
| so a test that never touches the database skips the migrations.
|
*/

pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
    ->in('Feature');

/*
| The admin panel is not marked as default(), so Filament tests select it explicitly.
*/

pest()->beforeEach(fn () => Filament::setCurrentPanel('admin'))
    ->in('Feature/Filament');
