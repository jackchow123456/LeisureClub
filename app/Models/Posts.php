<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    // 我是测试代码3
    public function user()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id', 'id');
    }
}
