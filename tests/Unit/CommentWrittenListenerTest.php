<?php

namespace Tests\Unit;

use App\Events\CommentWritten;
use App\Listeners\CommentWrittenListener;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentWrittenListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testCommentWrittenListener()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $listener = new CommentWrittenListener();
        $listener->handle(new CommentWritten($comment));

        $user->refresh();

        $this->assertTrue($user->hasAchievement('First Comment Written'));
        $this->assertTrue($user->hasBadge('Beginner'));
    }
}
