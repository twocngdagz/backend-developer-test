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

    public function testGetNextAchievements()
    {
        $user = User::factory()->create();

        // Unlock some achievements
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->refresh();
        // Get the next available achievement
        $nextAchievements = $user->getNextAchievements();

        $lockAchievements = [
            '10 Lessons Watched',
            '25 Lessons Watched',
            '50 Lessons Watched',
            'First Comment Written',
            '3 Comments Written',
            '5 Comments Written',
            '10 Comments Written',
            '20 Comments Written',
        ];

        $this->assertEquals($lockAchievements, $nextAchievements);
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
        $nextAchievement = $user->getNextAchievements();

        $this->assertEmpty($nextAchievement);
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

    public function testGetCurrentBadge()
    {
        // Create a user
        $user = User::factory()->create();

        // Unlock some achievements for the user
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->unlockAchievement('10 Lessons Watched');
        $user->unlockAchievement('First Comment Written');
        $user->refresh();

        // Update badges
        $user->updateBadges();

        // Call the currentBadge method
        $currentBadge = $user->currentBadge();

        // Assert the user's current badge
        $this->assertEquals('Intermediate', $currentBadge);
    }

    public function testGetNextBadge()
    {
        // Create a user
        $user = User::factory()->create();

        // Unlock some achievements for the user
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->unlockAchievement('10 Lessons Watched');
        $user->unlockAchievement('First Comment Written');
        $user->refresh();

        // Update badges
        $user->updateBadges();

        // Call the nextBadge method
        $nextBadge = $user->nextBadge();

        // Assert the user's next badge
        $this->assertEquals('Advanced', $nextBadge);
    }

    public function testRemainingToUnlockNextBadge()
    {
        // Create a user
        $user = User::factory()->create();

        // Unlock some achievements for the user
        $user->unlockAchievement('First Lesson Watched');
        $user->unlockAchievement('5 Lessons Watched');
        $user->unlockAchievement('10 Lessons Watched');
        $user->unlockAchievement('First Comment Written');
        $user->refresh();

        // Update badges
        $user->updateBadges();

        // Call the remainingToUnlockNextBadge method
        $remainingToUnlockNextBadge = $user->remainingToUnlockNextBadge();

        // Assert the remaining achievements to unlock the next badge
        $this->assertEquals(4, $remainingToUnlockNextBadge);
    }

    public function testGetNextBadgeAllUnlocked()
    {
        // Create a user
        $user = User::factory()->create();

        // Unlock all achievements for the user
        $user->achievements()->attach(Achievement::all()->pluck('id'));
        $user->refresh();
        // Update badges
        $user->updateBadges();

        // Call the nextBadge method when all badges are already unlocked
        $nextBadge = $user->nextBadge();

        // Assert that nextBadge returns null when all badges are unlocked
        $this->assertNull($nextBadge);
    }
}
