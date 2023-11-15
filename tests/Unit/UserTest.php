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
        Achievement::factory()->create(['name' => 'Test Achievement']);
        //Unlock the achievement for the user
        $user->unlockAchievement('Test Achievement');
        $this->assertTrue($user->refresh()->hasAchievement('Test Achievement'));
    }
}
