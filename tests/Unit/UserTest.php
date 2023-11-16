<?php

namespace Tests\Unit;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUnlockAchievement()
    {
        $user = User::factory()->create();
        //Unlock the achievement for the user
        $user->unlockAchievement('First Lesson Watched');
        $this->assertTrue($user->refresh()->hasAchievement('First Lesson Watched'));
    }

    public function testGetNextAchievement()
    {
        $user = User::factory()->create();

        // Unlock some achievements
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->refresh();
        // Get the next available achievement
        $nextAchievement = $user->getNextAchievement();

        $this->assertEquals('10 Lessons Watched', $nextAchievement->name);
    }

    public function testUpdateBadges()
    {
        $user = User::factory()->create();

        // Unlock some achievements
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->unlockAchievement('First Comment Written');
        $user->unlockAchievement('3 Comments Written');
        $user->refresh();
        // Update badges
        $unlockedBadges = $user->updateBadges();
        $user->refresh();

        $this->assertContains('Intermediate', $unlockedBadges);
        $this->assertTrue($user->hasBadge('Intermediate'));
    }

    public function testGetUnlockedAchievements()
    {
        $user = User::factory()->create();

        // Unlock some achievements
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->unlockAchievement('First Comment Written');
        $user->refresh();
        // Get unlocked achievements
        $unlockedAchievements = $user->getUnlockedAchievements();

        $this->assertContains('First Lesson Watched', $unlockedAchievements);
        $this->assertContains('5 Lessons Watched', $unlockedAchievements);
        $this->assertContains('First Comment Written', $unlockedAchievements);
    }

    public function testGetNextAchievementAllUnlocked()
    {
        $user = User::factory()->create();

        // Unlock all achievements
        Achievement::all()->each(function ($achievement) use ($user) {
            $user->unlockAchievement($achievement->name);
        });
        $user->refresh();
        // Get the next available achievement when all are unlocked
        $nextAchievement = $user->getNextAchievement();

        $this->assertNull($nextAchievement);
    }

    public function testUpdateBadgesWithoutAchievements()
    {
        $user = User::factory()->create();

        // Update badges without unlocking any achievements
        $unlockedBadges = $user->updateBadges();

        $this->assertContains('Beginner', $unlockedBadges);
    }

    public function testUnlockSameAchievementTwice()
    {
        $user = User::factory()->create();

        // Unlock the achievement for the user
        $user->unlockAchievement('First Lesson Watched');
        $user->refresh();
        // Try to unlock the same achievement again
        $user->unlockAchievement('First Lesson Watched');

        $this->assertCount(1, $user->achievements);
    }
}
