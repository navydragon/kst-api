<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Администратор',
                'email' => 'test1@test.com',
                'password' => Hash::make('111111'),
                'lastname' => 'Тестовый',
                'firstname' => 'Администратор',
                'middlename' => '1',
                'login' => 'test1',
                'filial_id' => 1,
                'role_id' => 1,
                'division' => 'test1',
                'job' => 'test1',
                'phone' => 'test1',
                'code' => null,
                'valid_till' => null,
                'change_password' => 0,
            ],
        ]);
        $lastUserId = DB::table('users')->max('id');
        DB::table('test_user')->insert([
            'user_id' => $lastUserId,
            'test_id' => 1,
            'attempts' => 99,
            'used_attempts' => 0,
            'use_photo' => 0,
            'use_train' => 1,
            'current_score' => 0,
            'current_percent' => 0,
            'is_passed' => 0,
        ]);
        DB::table('users')->insert([
            [
                'name' => 'Пользователь',
                'email' => 'test2@test.com',
                'password' => Hash::make('111111'),
                'lastname' => 'Тестовый',
                'firstname' => 'Пользователь',
                'middlename' => '1',
                'login' => 'test2',
                'filial_id' => 1,
                'role_id' => 2,
                'division' => 'test2',
                'job' => 'test2',
                'phone' => 'test2',
                'code' => null,
                'valid_till' => null,
                'change_password' => 0,
            ],
        ]);
        $lastUserId = DB::table('users')->max('id');
        DB::table('test_user')->insert([
            'user_id' => $lastUserId,
            'test_id' => 1,
            'attempts' => 99,
            'used_attempts' => 0,
            'use_photo' => 0,
            'use_train' => 1,
            'current_score' => 0,
            'current_percent' => 0,
            'is_passed' => 0,
        ]);
        
        DB::table('users')->insert([
            [
                'name' => 'Тестируемый',
                'email' => 'test3@test.com',
                'password' => Hash::make('111111'),
                'lastname' => 'Тестируемый',
                'firstname' => 'Пользователь',
                'middlename' => '1',
                'login' => 'test2',
                'filial_id' => 1,
                'role_id' => 3,
                'division' => 'test3',
                'job' => 'test3',
                'phone' => 'test3',
                'code' => '111111',
                'valid_till' => null,
                'change_password' => 0,
            ],
        ]);
        $lastUserId = DB::table('users')->max('id');
        DB::table('test_user')->insert([
            'user_id' => $lastUserId,
            'test_id' => 1,
            'attempts' => 99,
            'used_attempts' => 0,
            'use_photo' => 0,
            'use_train' => 1,
            'current_score' => 0,
            'current_percent' => 0,
            'is_passed' => 0,
        ]);
            
    }
}
