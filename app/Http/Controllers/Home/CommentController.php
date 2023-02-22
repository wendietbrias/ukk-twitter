<?php 

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

//models
use App\Models\Tweets;
use App\Models\Comment;

class CommentController extends Controller {

    public function comment($id){
        $tweet_user = Tweets::with(['comments.user' , 'user'])->where('id' , $id)->first();
        return response()->json($tweet_user , 200);
    }

    public function createComment(Request $request) {

    $format_image = null;
    //memisahkan hashtag dan tweet
    $split_comment = explode("#" , $request->comment);
    //menghapus tweet di array
    $slices_comment = array_slice($split_comment,1,count($split_comment) - 1);

    //cek apakah ada image atau tidak
    if($request->hasFile('image')) {
        $storage = Storage::disk('comment_image');

        $format_image = Auth::user()->username . '_' . Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();

        $storage->putFileAs(null,$request->file('image') , $format_image, []);
    }


        $created = Comment::create([
            "comment"=>$split_comment[0],
            "tweet_id"=>$request->tweet_id,
            "tag"=>implode("",$slices_comment),
            "user_id"=>Auth::user()->id ,
            "media"=>$format_image
        ]);

        if($created) {
            $tweet_user = Tweets::with(['comments.user' , 'user'])->where('id' ,$request->tweet_id)->get();
            return response()->json($tweet_user);
        }

        return response()->json(['message'=>'failed to create comment'],  400);
    }

    public function deleteComment($id){
          $find_comment = Comment::where('id' , $id);

          if($find_comment) {
             $delete=  $find_comment->delete();

             if($find_comment->first()->media != null && $find_comment->first()->media != '') {
                 $storage = Storage::disk('comment_image');

                if($storage->exists($find_comment->first()->media)) {
                    $storage->delete($find_comment->first()->media);
                }
             }

             if($delete) {
                return response()->json(['message' => 'delete success']);
             }
          }
    }

    public function updateComment(Request $request) {
      
        //cek apakah id comment ada atau tidak
        if($request->id_update) {
            $find_tweet = Comment::where('id' , $request->id_update);
            $format_image = null;

            //cek apakah image ada atau tidak
            if($request->hasFile('image_update')) {
               $storage = Storage::disk('comment_image');
               
               if($find_tweet->first()->media != null && $find_tweet->first()->media != '' && $request->exists($find_tweet->first()->media)) {
                 $storage->delete($find_tweet->first()->media);
               }

               $format_image = Auth::user()->username . '_' . Str::random(12) . '.' . $request->file('image_update')->getClientOriginalExtension();
               $storage->putFileAs(null , $request->file('image_update') , $format_image ,[]);

            }

            //memisahkan tweet dan hashtag
            $split_comment = explode("#" , $request->comment);
            
            //menjadikan hashtag sebagai array
            $slices_comment = array_slice($split_comment ,1 ,count($split_comment) - 1);

            if($find_tweet->first()) {
                  $find_tweet->update([
                    'comment'=>$split_comment[0],
                    'tag'=>implode("" , $slices_comment),//convert array menjadi string
                    'media'=>$format_image != null ? $format_image : $find_tweet->first()->media
                 ]);

                 return response()->json(['message'=>'success update comment']);
            }

            return response()->json(['message'=>'comment not found']);
        } else {
            return response()->json(['message'=>'comment not found']);
        }
    }
}