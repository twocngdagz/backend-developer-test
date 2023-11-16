<?php

namespace App\Listeners;

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
            1 => $user->unlockAchievement('First Comment Written'),
            3 => $user->unlockAchievement('3 Comments Written'),
            5 => $user->unlockAchievement('5 Comments Written'),
            10 => $user->unlockAchievement('10 Comments Written'),
            20 => $user->unlockAchievement('20 Comments Written'),
            default => null,
        };

        $user->updateBadges();
    }
}
