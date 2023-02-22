<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//models
use App\Models\Tweets;
use App\Models\User;

class Likes extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'tweet_id'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id' , 'user_id');
    }

    public function tweet() {
        return $this->hasOne(Tweets::class, 'id', 'tweet_id');
    }
}
