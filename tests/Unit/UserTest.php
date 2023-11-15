<?php

namespace Tests\Unit;

use App\Models\Achievement;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUnlockAchievement()
    {
        $user = User::factory()->create();
        Achievement::factory()->create(['name' => 'Test Achievement']);
        //Unlock the achievement for the user
        $user->unlockAchievement('Test Achievement');
        $this->assertTrue($user->refresh()->hasAchievement('Test Achievement'));
    }

    public function testGetNextAchievement()
    {
        $user = User::factory()->create();
        $firstAchievement = Achievement::factory()->create(['name' => 'First Lesson Watched']);
        $secondAchievement = Achievement::factory()->create(['name' => '5 Lessons Watched']);
        $thirdAchievement = Achievement::factory()->create(['name' => '10 Lessons Watched']);

        // Unlock some achievements
        $user->unlockAchievement($firstAchievement->name);
        $user->unlockAchievement($secondAchievement->name);
        $user->refresh();
        // Get the next available achievement
        $nextAchievement = $user->getNextAchievement();

        $this->assertEquals($thirdAchievement->name, $nextAchievement->name);
    }
}
