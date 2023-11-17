<?php

namespace Tests\Unit;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // All user will initially have a Beginner badge
        $this->user = User::factory()
            ->hasAttached(
                Badge::find(1)
            )
            ->create();
    }

    public function testUnlockAchievement()
    {
        //Unlock the achievement for the user
        $this->user->unlockAchievement('First Lesson Watched');
        $this->assertTrue($this->user->refresh()->hasAchievement('First Lesson Watched'));
    }

    public function testGetNextAchievements()
    {
        // Unlock some achievements
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('5 Lessons Watched');
        $this->user->refresh();
        // Get the next available achievement
        $nextAchievements = $this->user->getNextAchievements();

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
        // Unlock some achievements
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('5 Lessons Watched');
        $this->user->unlockAchievement('First Comment Written');
        $this->user->unlockAchievement('3 Comments Written');
        $this->user->refresh();
        // Update badges
        $unlockedBadges = $this->user->updateBadges();
        $this->user->refresh();

        $this->assertContains('Intermediate', $unlockedBadges);
        $this->assertTrue($this->user->hasBadge('Intermediate'));
    }

    public function testGetUnlockedAchievements()
    {
        // Unlock some achievements
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('5 Lessons Watched');
        $this->user->unlockAchievement('First Comment Written');
        $this->user->refresh();
        // Get unlocked achievements
        $unlockedAchievements = $this->user->getUnlockedAchievements();

        $this->assertContains('First Lesson Watched', $unlockedAchievements);
        $this->assertContains('5 Lessons Watched', $unlockedAchievements);
        $this->assertContains('First Comment Written', $unlockedAchievements);
    }

    public function testGetNextAchievementAllUnlocked()
    {
        // Unlock all achievements
        Achievement::all()->each(function ($achievement) {
            $this->user->unlockAchievement($achievement->name);
        });
        $this->user->refresh();
        // Get the next available achievement when all are unlocked
        $nextAchievement = $this->user->getNextAchievements();

        $this->assertEmpty($nextAchievement);
    }

    public function testUpdateBadgesWithoutAchievements()
    {
        // Update badges without unlocking any achievements
        $unlockedBadges = $this->user->updateBadges();

        //return null no need to unlock Beginner badge since by default new user has Beginner badge
        $this->assertEquals(null, $unlockedBadges);
    }

    public function testUnlockSameAchievementTwice()
    {
        // Unlock the achievement for the user
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->refresh();
        // Try to unlock the same achievement again
        $this->user->unlockAchievement('First Lesson Watched');

        $this->assertCount(1, $this->user->achievements);
    }

    public function testGetCurrentBadge()
    {
        // Unlock some achievements for the user
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('5 Lessons Watched');
        $this->user->unlockAchievement('10 Lessons Watched');
        $this->user->unlockAchievement('First Comment Written');
        $this->user->refresh();

        // Update badges
        $this->user->updateBadges();

        // Call the currentBadge method
        $currentBadge = $this->user->currentBadge();

        // Assert the user's current badge
        $this->assertEquals('Intermediate', $currentBadge);
    }

    public function testGetNextBadge()
    {
        // Unlock some achievements for the user
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('5 Lessons Watched');
        $this->user->unlockAchievement('10 Lessons Watched');
        $this->user->unlockAchievement('First Comment Written');
        $this->user->refresh();

        // Update badges
        $this->user->updateBadges();

        // Call the nextBadge method
        $nextBadge = $this->user->nextBadge();

        // Assert the user's next badge
        $this->assertEquals('Advanced', $nextBadge);
    }

    public function testRemainingToUnlockNextBadge()
    {
        // Unlock some achievements for the user
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('5 Lessons Watched');
        $this->user->unlockAchievement('10 Lessons Watched');
        $this->user->unlockAchievement('First Comment Written');
        $this->user->refresh();

        // Update badges
        $this->user->updateBadges();

        // Call the remainingToUnlockNextBadge method
        $remainingToUnlockNextBadge = $this->user->remainingToUnlockNextBadge();

        // Assert the remaining achievements to unlock the next badge
        $this->assertEquals(4, $remainingToUnlockNextBadge);
    }

    public function testGetNextBadgeAllUnlocked()
    {
        // Unlock all achievements for the user
        $this->user->achievements()->attach(Achievement::all()->pluck('id'));
        $this->user->refresh();
        // Update badges
        $this->user->updateBadges();

        // Call the nextBadge method when all badges are already unlocked
        $nextBadge = $this->user->nextBadge();

        // Assert that nextBadge returns null when all badges are unlocked
        $this->assertNull($nextBadge);
    }

    public function testRemainingToUnlockNextBadgeAllUnlocked()
    {
        // Unlock all achievements for the user
        $this->user->achievements()->attach(Achievement::all()->pluck('id'));
        $this->user->refresh();
        // Update badges
        $this->user->updateBadges();

        // Call the remainingToUnlockNextBadge method when all badges are already unlocked
        $remainingToUnlockNextBadge = $this->user->remainingToUnlockNextBadge();

        // Assert that remainingToUnlockNextBadge returns 0 when all badges are unlocked
        $this->assertEquals(0, $remainingToUnlockNextBadge);
    }

    public function testNextAvailableAchievements()
    {
        // Unlock some achievements for the user
        $this->user->unlockAchievement('First Lesson Watched');
        $this->user->unlockAchievement('First Comment Written');
        $this->user->refresh();

        // Call the nextAvailableAchievements method
        $nextAvailableAchievements = $this->user->nextAvailableAchievements();

        // Assert the next available achievements
        $this->assertEquals(['5 Lessons Watched', '3 Comments Written'], $nextAvailableAchievements);
    }
}
