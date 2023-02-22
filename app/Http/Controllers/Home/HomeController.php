<?php 

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//models
use App\Models\Tweets;

class HomeController extends Controller {
    public function showHome(){
        // cek apakah user sudah login atau belum
        if(!Auth::check()) {
            return redirect()->route("auth.login.view");
        }

        //select user berdasarkan data terakhir
        $tweets = Tweets::with(['user'])->orderByDesc('created_at')->get();

        return view("home.home" , ['title'=>'Homepage' , 'tweets'=>$tweets]);
    }

    public function showCreateTweets() {
        // cek apakah user sudah login atau belum

        if(!Auth::check()) {
            return redirect()->route("auth.login.view" );
        }

        return view("home.createTweets" , ['title'=>'Create tweets']);
    }

    public function showProfile(){
        // cek apakah user sudah login atau belum

        if(!Auth::check()) {
            return redirect()->route("auth.login.view");
        }
        
        return view("home.profile" , ['title'=>'Profile']);
    }

    public function search(Request $request) {
        
    }

    public function showSearch() {
        
    }
}

?>