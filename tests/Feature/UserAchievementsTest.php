<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAchievementsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetUserAchievements()
    {
        // Create a user
        $user = User::factory()
            ->hasAttached(
                Badge::find(1)
            )
            ->create();

        $achievements = [
            'First Lesson Watched',
            '5 Lessons Watched',
            'First Comment Written',
        ];

        // Unlock some achievements for the user
        foreach ($achievements as $achievement) {
            $user->unlockAchievement($achievement);
        }

        // Send a request to the achievements endpoint
        $response = $this->get("/users/{$user->id}/achievements");

        // Assert the response is successful
        $response->assertStatus(200);

        // Decode the JSON response
        $data = $response->json();

        // Assert the response structure
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaining_to_unlock_next_badge',
        ]);

        // Assert the user's unlocked achievements
        $this->assertEquals($achievements, $data['unlocked_achievements']);

        // Assert the next available achievements
        $this->assertEquals(['10 Lessons Watched', '3 Comments Written'], $data['next_available_achievements']);

        // Assert the user's current badge
        $this->assertEquals('Beginner', $data['current_badge']);

        // Assert the next badge the user can earn
        $this->assertEquals('Intermediate', $data['next_badge']);

        // Assert the remaining achievements to unlock the next badge
        $this->assertEquals(1, $data['remaining_to_unlock_next_badge']);
    }
}
