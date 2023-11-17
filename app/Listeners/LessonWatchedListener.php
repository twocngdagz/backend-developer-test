<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
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
        $lesson = $event->lesson;

        // Logic to determine whether the user has unlocked a new achievement
        $lessonsWatchedCount = $user->lessons()->count();

        if ($lessonsWatchedCount == 1) {
            $user->unlockAchievement('First Lesson Watched');
            // User watched the first lesson, unlock the "First Lesson Watched" achievement
            event(new AchievementUnlocked('First Lesson Watched', $user));
        } elseif ($lessonsWatchedCount == 5) {
            $user->unlockAchievement('5 Lesson Watched');
            // User watched five lessons, unlock the "5 Lessons Watched" achievement
            event(new AchievementUnlocked('5 Lessons Watched', $user));
        } elseif ($lessonsWatchedCount == 10) {
            $user->unlockAchievement('10 Lesson Watched');
            // User watched five lessons, unlock the "10 Lessons Watched" achievement
            event(new AchievementUnlocked('10 Lessons Watched', $user));
        } elseif ($lessonsWatchedCount == 25) {
            $user->unlockAchievement('25 Lesson Watched');
            // User watched five lessons, unlock the "5 Lessons Watched" achievement
            event(new AchievementUnlocked('25 Lessons Watched', $user));
        } elseif ($lessonsWatchedCount == 50) {
            $user->unlockAchievement('50 Lesson Watched');
            // User watched five lessons, unlock the "50 Lessons Watched" achievement
            event(new AchievementUnlocked('50 Lessons Watched', $user));
        }

        // Logic to determine whether the user has unlocked a new badge
        $user->updateBadges(); // Assuming you have a method to update badges based on achievements
    }
}
