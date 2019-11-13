<?php

use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder
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
                'user_id' => 2,
                'post_id' => 1,
                'content' => '很好*1',
            ],
            [
                'user_id' => 2,
                'post_id' => 1,
                'content' => '很好*2',
            ],
            [
                'user_id' => 2,
                'post_id' => 1,
                'content' => '很好*3',
            ]
        ];
        \App\Models\Comments::insert($arr);
    }
}
