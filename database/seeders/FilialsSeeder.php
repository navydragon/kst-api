<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('filials')->insert([
            ['name' => 'РУТ (МИИТ)', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'rut'],
            ['name' => 'ЦБТ', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'cbt'],
            ['name' => 'Западно-Сибирская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'wsr'],
            ['name' => 'Московская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'msk'],
            ['name' => 'Восточно-Сибирская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'esrr'],
            ['name' => 'Северо-Кавказская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'skzd'],
            ['name' => 'Свердловская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'svrw'],
            ['name' => 'Дальневосточная железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'dvgd'],
            ['name' => 'Южно-Уральская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'surw'],
            ['name' => 'Северная железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'nrr'],
            ['name' => 'Куйбышевская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'kbsh'],
            ['name' => 'Горьковская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'grw'],
            ['name' => 'Забайкальская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'zrw'],
            ['name' => 'Красноярская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'krw'],
            ['name' => 'Центральная дирекция инфраструктуры', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'cdi'],
            ['name' => 'Приволжская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'pvrr'],
            ['name' => 'Октябрьская железная дорога', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'oct'],
            ['name' => 'Трансэнерго', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'teo'],
            ['name' => 'Дирекция по эксплуатации зданий и сооружений', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), 'prefix' => 'dez'],
        ]);
    }
}
