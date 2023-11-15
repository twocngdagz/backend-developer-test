<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('badges')->truncate();
        $badges =
            [
                [
                    'name' => 'Beginner'
                ],
                [
                    'name' => 'Intermediate'
                ],
                [
                    'name' => 'Advanced'
                ],
                [
                    'name' => 'Master',
                ]
            ];
        collect($badges)->each(fn ($badge) => Badge::create($badge));
    }
}
