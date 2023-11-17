<?php

namespace Tests\Unit;

use App\Events\LessonWatched;
use App\Listeners\LessonWatchedListener;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonWatchedListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testLessonWatchListener()
    {
        $user = User::factory()
            ->hasAttached(
                Badge::find(1)
            )
            ->create();
        $lesson = Lesson::factory()->create();

        $user->refresh();
        $listener = new LessonWatchedListener();
        $listener->handle(new LessonWatched($lesson, $user));
        $user->refresh();

        $this->assertTrue($user->hasAchievement('First Lesson Watched'));
        $this->assertTrue($user->hasBadge('Beginner'));
    }
}
