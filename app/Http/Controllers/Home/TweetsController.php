<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

//models
use App\Models\Tweets;
use App\Models\Tags;
use App\Models\TweetTags;

class TweetsController extends Controller {

  public function tweet() {

    //mengambil seluruh tweet 
     $allTweet = Tweets::with(['comments','user' , 'likes'])->orderByDesc('created_at')->get();
     return response()->json($allTweet);
  }



  public function create_tweet(Request $request) { 

    $validator = $request->validate([
      'tweet'=>['required', 'min:10']
    ]);

    //cek panjang karakter tweet yang dimasukan user
    if(strlen($request->tweet) > 250) {
      return response()->json(['message'=>'your tweet is to long']);
    }
  
    //memisahkan hashtag dan tweet
    $split_tweet = explode("#" , $request->tweet);

    //menghapus tweet di array
    $slices_tweet = array_slice($split_tweet,1,count($split_tweet) - 1);


    //deklarasi format image
    $format_image = null;
    
    
    //cek apakah gambar ada atau tidak
      if($request->hasFile('image')) {
         $storage = Storage::disk('tweet_image');
         //menformat gambar yang akan di masukan ke storage
         $format_image = Auth::user()->username . '_' . Str::random(12) . '_' . '.' . $request->file('image')->getClientOriginalExtension();
         //memasukan gambar ke storage
         $storage->putFileAs(null , $request->file('image') ,$format_image, null);
      }
    

      $created = Tweets::create([
        'tweet'=>$split_tweet[0],
        'media'=>$format_image != null ? $format_image : null,
        'user_id'=>Auth::user()->id,
        'tag'=>implode($slices_tweet)//convert array ke string
      ]);

      //cek apakah data ada dibuat atau tidak
      if($created) {
        return response()->json(['message'=>'success created','status'=>200] , 200);
      }

      //menampilkan message jika error
      return response()->json(['message'=>'failed create tweet' ,'status'=>400] , 400);
  }

  public function detail_tweet($id) {
     //mencari tweet dengan berdasarkan id
      $find_tweet = Tweets::with(['comments.user' ,'user'])->where('id', $id)->first();

      //cek apakah tweet ditemukan atau tidak
      if($find_tweet) {
         return view('home.detailPost' , ['data'=>$find_tweet]);
      }

      return back();
  }

  public function Likes() {
     
  }
}