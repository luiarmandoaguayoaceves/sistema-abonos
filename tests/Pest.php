<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        $this->actingAs(User::factory()->create());
    })
    ->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});