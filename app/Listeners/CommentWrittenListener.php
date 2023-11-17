<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;

class CommentWrittenListener
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
    public function handle(CommentWritten $event): void
    {
        $user = $event->comment->user;
        $commentsWrittenCount = $user->comments()->count();

        if ($commentsWrittenCount == 1) {
            $user->unlockAchievement('First Comment Written');
            // User watched the first lesson, unlock the "First Comment Written" achievement
            event(new AchievementUnlocked('First Comment Written', $user));
        } elseif ($commentsWrittenCount == 3) {
            $user->unlockAchievement('3 Comments Written');
            // User watched five lessons, unlock the "'3 Comments Written" achievement
            event(new AchievementUnlocked('3 Comments Written', $user));
        } elseif ($commentsWrittenCount == 5) {
            $user->unlockAchievement('5 Comments Written');
            // User watched five lessons, unlock the "5 Comments Written" achievement
            event(new AchievementUnlocked('5 Comments Written', $user));
        } elseif ($commentsWrittenCount == 10) {
            $user->unlockAchievement('10 Comments Written');
            // User watched five lessons, unlock the "10 Comments Written" achievement
            event(new AchievementUnlocked('10 Comments Written', $user));
        } elseif ($commentsWrittenCount == 10) {
            $user->unlockAchievement('20 Comments Written');
            // User watched five lessons, unlock the "20 Comments Written" achievement
            event(new AchievementUnlocked('20 Comments Written', $user));
        }

        $user->updateBadges();
    }
}
