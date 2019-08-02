<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
            [
                'name' => 'jack',
                'email' => '775893055@qq.com',
                'password' => encrypt('123456'),
            ],
            [
                'name' => 'hurry',
                'email' => '775893056@qq.com',
                'password' => encrypt('123456'),
            ],
        ];
        User::insert($arr);
    }
}
