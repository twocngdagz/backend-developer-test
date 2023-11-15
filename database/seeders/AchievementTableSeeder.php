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
                'name' => 'First Lesson Watched'
                ],
                [
                    'name' => '5 Lessons Watched'
                ],
                [
                    'name' => '10 Lessons Watched'
                ],
                [
                    'name' => '25 Lessons Watched',
                ],
                [
                    'name' => '50 Lessons Watched',
                ],
                [
                    'name' => 'First Comment Written',
                ],
                [
                    'name' => '3 Comments Written',
                ],
                [
                    'name' => '5 Comments Written'
                ],
                [
                    'name' => '10 Comments Written'
                ],
                [
                    'name' => '20 Comments Written'
                ]
            ];
        collect($achievements)->each(fn ($achievement) => Achievement::create($achievement));
    }
}
