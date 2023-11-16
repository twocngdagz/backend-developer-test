<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AchievementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('achievements')->truncate();
        Schema::enableForeignKeyConstraints();
        $achievements =
            [
                [
                    'name' => 'First Lesson Watched',
                    'type' => 'lesson',
                    'order' => 1,
                ],
                [
                    'name' => '5 Lessons Watched',
                    'type' => 'lesson',
                    'order' => 2,
                ],
                [
                    'name' => '10 Lessons Watched',
                    'type' => 'lesson',
                    'order' => 3,
                ],
                [
                    'name' => '25 Lessons Watched',
                    'type' => 'lesson',
                    'order' => 4,
                ],
                [
                    'name' => '50 Lessons Watched',
                    'type' => 'lesson',
                    'order' => 5,
                ],
                [
                    'name' => 'First Comment Written',
                    'type' => 'comment',
                    'order' => 1,
                ],
                [
                    'name' => '3 Comments Written',
                    'type' => 'comment',
                    'order' => 2,
                ],
                [
                    'name' => '5 Comments Written',
                    'type' => 'comment',
                    'order' => 3,
                ],
                [
                    'name' => '10 Comments Written',
                    'type' => 'comment',
                    'order' => 4,
                ],
                [
                    'name' => '20 Comments Written',
                    'type' => 'comment',
                    'order' => 5,
                ]
            ];
        collect($achievements)->each(fn ($achievement) => Achievement::create($achievement));
    }
}
