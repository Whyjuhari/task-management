<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class DeadlineIndicatorTest extends TestCase
{
    public function test_deadline_indicator_uses_the_required_time_ranges(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        $this->blade('<x-deadline-indicator :deadline="$deadline" />', [
            'deadline' => now()->addDays(4),
        ])
            ->assertSee('Lebih dari 3 hari')
            ->assertSee('text-success', false);

        $this->blade('<x-deadline-indicator :deadline="$deadline" />', [
            'deadline' => now()->addDays(2),
        ])
            ->assertSee('1–3 hari')
            ->assertSee('text-amber-700', false);

        $this->blade('<x-deadline-indicator :deadline="$deadline" />', [
            'deadline' => now()->addHours(12),
        ])
            ->assertSee('Kurang dari 1 hari')
            ->assertSee('text-danger', false);

        $this->blade('<x-deadline-indicator :deadline="$deadline" />', [
            'deadline' => now()->subMinute(),
        ])
            ->assertSee('Deadline lewat')
            ->assertSee('text-danger', false);
    }
}
