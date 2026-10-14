<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        $rows = [
            [
                'key' => 'show_alert',
                'value' => 'false',
            ],
            [
                'key' => 'alert_state',
                'value' => 'info',
            ],
            [
                'key' => 'alert_message',
                'value' => 'Для прохождения тестирования зарегистрируйтесь: укажите ФИО, email и филиал.',
            ],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('settings')->where('key', $row['key'])->exists();
            if ($exists) {
                continue;
            }
            DB::table('settings')->insert([
                'key' => $row['key'],
                'value' => $row['value'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
