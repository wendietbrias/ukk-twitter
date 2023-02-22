<?php 

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// models 
use App\Models\Likes;
use App\Models\User;
use App\Models\Tweets;

class LikeController extends Controller {
    public function like(Request $request){
        $find_like = Likes::where(['user_id'=>$request->user_id , 'tweet_id'=>$request->tweet_id]);

        if($find_like) {
            $deleted = $find_like->delete();

            if($deleted) {
                 return response()->json(['message'=>'unliked']);
            }
        }

       $created = Likes::create([
           'user_id'=>$request->user_id,
           'tweet_id'=>$request->tweet_id
       ]);

       if($created) {
          return response()->json(['message'=>'liked']);
       }
    }
}