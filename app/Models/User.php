<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class)->withTimestamps();
    }

    public function unlockAchievement(string $achievementName): bool
    {
        // Check if the user has already unlocked this achievement
        if (! $this->hasAchievement($achievementName)) {

            // If not, unlock the achievement for the user
            $achievement = Achievement::where('name', $achievementName)->first();

            $this->achievements()->attach($achievement->id);

            return true; // Indicate that the achievement was unlocked
        }

        return false; // Indicate that the achievement was not unlocked (already unlocked)
    }

    public function hasAchievement(string $achievementName): bool
    {
        return $this->achievements->contains('name', $achievementName);
    }

    public function getNextAchievements(): array
    {
        // Get unlocked achievements for the user
        $unlockedAchievements = $this->achievements->pluck('name')->toArray();

        // Find the first achievement that the user hasn't unlocked
        $nextAchievements = Achievement::all()->filter(function ($achievement) use ($unlockedAchievements) {
            return ! in_array($achievement->name, $unlockedAchievements);
        });

        return $nextAchievements?->pluck('name')->toArray();
    }

    public function updateBadges(): ?array
    {
        $unlockedBadges = collect([]);

        // Check if the user has unlocked any new badges
        $badgeLevels = Badge::all()->pluck('name', 'level')->toArray();

        foreach ($badgeLevels as $requiredAchievements => $badgeName) {
            if ($this->achievements->count() >= $requiredAchievements && ! $this->hasBadge($badgeName)) {
                $badge = Badge::where('name', $badgeName)->first();
                $this->badges()->attach($badge->id);
                $unlockedBadges->push($badgeName);
            }
        }

        return $unlockedBadges->isEmpty() ? null : $unlockedBadges->toArray();
    }

    public function hasBadge(string $badgeName): bool
    {
        return $this->badges->contains('name', $badgeName);
    }

    public function getUnlockedAchievements(): array
    {
        return $this->achievements->pluck('name')->toArray();
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function currentBadge(): string
    {
        // Define badge thresholds based on the number of achievements
        $badgeThresholds = Badge::orderBy('level', 'desc')->get()->pluck('level', 'name')->toArray();

        // Count the number of unlocked achievements for the user
        $unlockedAchievements = $this->achievements->count();
        // Determine the user's current badge based on thresholds
        foreach ($badgeThresholds as $badge => $threshold) {
            if ($unlockedAchievements >= $threshold) {
                return $badge;
            }
        }

        // If none of the thresholds match, return the last badge
        return end($badgeThresholds);
    }

    public function nextBadge(): ?string
    {
        // Define badge thresholds based on the number of achievements
        $badgeThresholds = Badge::orderBy('level')->get();

        // Count the number of unlocked achievements for the user
        $unlockedAchievements = $this->achievements->count();

        // Find the next badge based on the user's achievements
        foreach ($badgeThresholds as $badge) {
            if ($unlockedAchievements < $badge->level) {
                return $badge->name;
            }
        }

        return null;
    }

    public function remainingToUnlockNextBadge(): int
    {
        // Define badge thresholds based on the number of achievements
        $badgeThresholds = Badge::orderBy('level')->get();

        // Count the number of unlocked achievements for the user
        $unlockedAchievements = $this->achievements->count();

        // Find the next badge based on the user's achievements
        foreach ($badgeThresholds as $badge) {
            if ($unlockedAchievements < $badge->level) {
                return $badge->level - $unlockedAchievements;
            }
        }

        // If all badges are unlocked, return 0 or appropriate value
        return 0; // Or any appropriate value indicating all badges are unlocked
    }
}
