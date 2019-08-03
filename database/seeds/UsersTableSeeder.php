<?php

use Illuminate\Database\Seeder;

use App\User;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(HasherContract $hasher)
    {
        $arr = [
            [
                'name' => 'jack',
                'email' => '775893055@qq.com',
                'password' => $hasher->make('123456'),
                'mobile' => '15015587480',
            ],
            [
                'name' => 'hurry',
                'email' => '775893056@qq.com',
                'password' => $hasher->make('123456'),
                'mobile' => '15015587481',
            ],
        ];
        User::insert($arr);
    }
}
