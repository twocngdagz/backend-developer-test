<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\LessonWatched;

class LessonWatchedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LessonWatched $event): void
    {
        $user = $event->user;

        // Logic to determine whether the user has unlocked a new achievement
        $lessonsWatchedCount = $user->lessons()->count();

        match ($lessonsWatchedCount) {
            1 => event(new AchievementUnlocked('First Lesson Watched', $user)),
            5 => event(new AchievementUnlocked('5 Lessons Watched', $user)),
            10 => event(new AchievementUnlocked('10 Lessons Watched', $user)),
            25 => event(new AchievementUnlocked('25 Lessons Watched', $user)),
            50 => event(new AchievementUnlocked('50 Lessons Watched', $user)),
            default => null,
        };

        $userBadgesCount = $user->badges()->count();

        match ($userBadgesCount) {
            4 => event(new BadgeUnlocked('Intermediate', $user)),
            8 => event(new BadgeUnlocked('Advanced', $user)),
            10 => event(new BadgeUnlocked('Master', $user)),

            default => null,
        };

        // Logic to determine whether the user has unlocked a new badge
        $user->updateBadges(); // Assuming you have a method to update badges based on achievements
    }
}
