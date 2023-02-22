<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//models
use App\Models\User;
use App\Models\Comment;
use App\Models\Likes;

class Tweets extends Model
{
    use HasFactory;

    public function user() {
       return $this->hasOne(User::class, 'id' , 'user_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'tweet_id' , 'id')->orderByDesc('created_at');
    }

    public function likes(){
        return $this->hasMany(Likes::class, 'tweet_id' , 'id');
    }
 
    protected $table = 'tweets';

    protected $fillable = [
        'tweet',
        'tag',
        'media',
        'user_id'
    ];

    protected $guarded = [
        'id'
    ];
    
}
