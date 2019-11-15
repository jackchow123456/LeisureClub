<?php

use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
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
                'user_id' => 1,
                'title' => '论演员的自我修养1',
                'content' => '论演员的自我修养1，论演员的自我修养1，论演员的自我修养1',
                'published_date' => '2019-11-11',
            ],
            [
                'user_id' => 1,
                'title' => '论演员的自我修养2',
                'content' => '论演员的自我修养2，论演员的自我修养2，论演员的自我修养2',
                'published_date' => '2019-11-11',
            ],
            [
                'user_id' => 1,
                'title' => '论演员的自我修养3',
                'content' => '论演员的自我修养3，论演员的自我修养3，论演员的自我修养3',
                'published_date' => '2019-11-11',
            ],

        ];
        \App\Models\Posts::insert($arr);
    }
}
