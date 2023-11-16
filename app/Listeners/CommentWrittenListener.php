<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
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

        match ($commentsWrittenCount) {
            1 => event(new AchievementUnlocked('First Comment Written', $user)),
            3 => event(new AchievementUnlocked('3 Comments Written', $user)),
            5 => event(new AchievementUnlocked('5 Comments Written', $user)),
            10 => event(new AchievementUnlocked('10 Comments Written', $user)),
            20 => event(new AchievementUnlocked('20 Comments Written', $user)),
            default => null,
        };

        $user->updateBadges();

        $userBadgesCount = $user->badges()->count();

        match ($userBadgesCount) {
            4 => event(new BadgeUnlocked('Intermediate', $user)),
            8 => event(new BadgeUnlocked('Advanced', $user)),
            10 => event(new BadgeUnlocked('Master', $user)),

            default => null,
        };
    }
}
