<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    public function user() {
        return $this->belongsTo(User::class, 'user_id' , 'id');
    }

    protected $fillable = [
        'id',
        'comment',
        'tag',
        'user_id',
        'tweet_id',
        'media'
    ];

    protected $guarded = [
        'id'
    ];
}
