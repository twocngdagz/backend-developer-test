<?php

namespace Tests\Unit;

use App\Events\BadgeUnlocked;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeUnlockedEventTest extends TestCase
{
    use RefreshDatabase;

    public function testBadgeUnlockedEvent(): void
    {
        Event::fake();
        $user = User::factory()->create();

        //Unlock the achievement for the user
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('First Comment Written');
        $user->unlockAchievement('5 Lessons Watched');
        $user->unlockAchievement('3 Comments Written');
        $user->refresh();
        $user->updateBadges();

        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($user) {
            return $event->name === 'Beginner' && $event->user->id === $user->id;
        });
        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($user) {
            return $event->name === 'Intermediate' && $event->user->id === $user->id;
        });
    }
}
