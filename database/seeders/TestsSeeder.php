<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tests')->insert([
            [
                'name' => 'Тестирование при назначении на должность',
                'link' => 'yandex.ru',
                'base_attempts' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'questions' => 40,
                'passing_score' => 28,
                'passing_percent' => 70,
            ],
        ]);
    }
}
