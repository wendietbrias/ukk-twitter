<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Tweets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

//models
use App\Models\User;

class ProfileController extends Controller {
    public function updateProfile(Request $request) {
        $find_user = User::where('id' , Auth::id());

        if($find_user->first()) {
             $updated = $find_user->update([
                  'username'=>$request->username,
                  'display_name'=>$request->display_name,
                  'bio'=>$request->bio,
                  'email'=>$request->email
             ]);

             if($updated) {
                return response()->json($find_user->first());
             }
        }
    }

    public function updateAvatar(Request $request){
        $find_user = User::where('id' , Auth::id());
        
        if($find_user->first()) {

            //memasukan gambar yang diinput user kedalam folder storage
              if($request->hasFile('image')){
                  $storage = Storage::disk('profile_user');

                  //cek jika gambar sebelumnya sama dengan yang baru maka hapus gambar sebelumnya
                  if ($find_user->first()->media != null && $storage->exists($find_user->first()->media)) {
                    $storage->delete($find_user->first()->media);
                }

                //memnformat gambar
                  $format_image = $request->username . '_' . Str::random(12) . '_' . '.' . $request->file('image')->getClientOriginalExtension();
                  //memasukan gambar
                  $storage->putFileAs(null , $request->file('image'),$format_image , []);
              }

              $updated = $find_user->update([
                'media'=>$format_image 
              ]);

              //cek apakah data terupdate atau tidak
              if($updated) {
                return response()->json($find_user->first());
              }

              return response()->json(['message'=>'faild update avatar']);
        }
    }

    public function userTweet() {
        $user_tweets = Tweets::with(['user' , 'comments', 'likes'])->where('user_id'  , Auth::user()->id)->get();
 
        //cek apakah tweet user ada atau tidak
        if($user_tweets){
          return response()->json($user_tweets);
        }
    }

    public function delete_tweet($id){
      $find_tweet = Tweets::where('id' , $id);
      $storage = Storage::disk('tweet_image');
      //mengecek apakah id tweet nya ada
        if($id) {
            
           //menghapus gambar yang di store di folder storage
            if($find_tweet->first()->media != null && $storage->exists($find_tweet->first()->media)){
               $storage->delete($find_tweet->first()->media);
            }

            $delete = $find_tweet->delete();

            //cek apakah data sudah terdelete atau belum
            if($delete) {
              return response()->json(['message'=>'success delete tweet' , 'status'=>200]);
            }
        }
    }

    public function update_tweet(Request $request ,$id) {
      //cari tweet dengan id
      $find_tweet = Tweets::find($id);
      $format_image = null;

      // return dd($find_tweet);

      //cek id dan tweet apakah ditemukan atau tidak
        if($id && $find_tweet){

          //memisahkan hashtag dan tweet
           $split_tweet = explode("#" , $request->tweet);

           //menghapus tweet di array
            $slices_tweet = array_slice($split_tweet,1,count($split_tweet) - 1);


          //cek apakah file gambar ada atau tidak
          if($request->hasFile('image_tweet') && $request->file('image_tweet') != null && $request->file('image_tweet') != ''){
              $storage = Storage::disk('tweet_image');

              if($find_tweet->media != null && $find_tweet->media != '' && $storage->exists($find_tweet->media)){
                  $storage->delete($find_tweet->media);
                }
                
                $format_image = $split_tweet[0] . '_' . Str::random(12) . '_' . '.' . $request->file('image_tweet')->getClientOriginalExtension();
                $storage->putFileAs(null,$request->file('image_tweet') , $format_image, []);

          }
        
             $find_tweet->tweet = $split_tweet[0];
             $find_tweet->tag = implode("" ,$slices_tweet);
             $find_tweet->media = $format_image == null ? $find_tweet->media : $format_image;

             //menyimpan data tweet
             $saved = $find_tweet->save();

             //cek apakah data tersimpan atau tidak
             if($saved){
              return response()->json(['message'=>'everything is up to date']);
             }

             return response()->json(['message'=>'failed to update tweet']);
        }
    }
}