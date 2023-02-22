<?php 

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//models

use App\Models\Tweets;
use App\Models\Comment;

class SearchController extends Controller {

    public function search(Request $request) {

        if($request->get('query') != ''){
            $find_tweet = Tweets::with(['user', 'likes', 'comments.user'])->where('tag' , 'LIKE','%' .$request->get('query').'%')->orderByDesc('created_at')->get();
            $find_comment = Comment::with('user')->where('tag' , 'LIKE' , '%'.$request->get('query').'%')->orderByDesc('created_at')->get();
            return response()->json(['tweets'=>$find_tweet, 'comments'=>$find_comment]);
        }

        return response()->json(['tweets'=>[], 'comments'=>[]]);
    }
}