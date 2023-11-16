<?php

namespace Tests\Unit;

use App\Events\LessonWatched;
use App\Listeners\LessonWatchedListener;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonWatchedListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testLessonWatchListener()
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson->id);

        $listener = new LessonWatchedListener();
        $listener->handle(new LessonWatched($lesson, $user));

        $user->refresh();

        $this->assertTrue(true, $user->hasAchievement('First Lesson Watched'));
        $this->assertTrue(true, $user->hasBadge('Intermediate'));
    }
}
