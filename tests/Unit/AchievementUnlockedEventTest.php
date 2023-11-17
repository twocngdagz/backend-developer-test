<?php

namespace Tests\Unit;

use App\Events\AchievementUnlocked;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementUnlockedEventTest extends TestCase
{
    use RefreshDatabase;

    public function testAchievementUnlockedEvent(): void
    {
        Event::fake();
        $user = User::factory()->create();
        //Unlock the achievement for the user
        $user->unlockAchievement('First Lesson Watched');

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user) {
            return $event->name === 'First Lesson Watched' && $event->user->id === $user->id;
        });
    }
}
