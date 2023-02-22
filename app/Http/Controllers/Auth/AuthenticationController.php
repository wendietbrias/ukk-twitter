<?php 

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

//models
use App\Models\User;

class AuthenticationController extends Controller {
    public function showLogin() {

        //cek jika user sudah login maka user tidak bisa login lagi 
        if(Auth::check()) {
            return redirect()->route("home.view"  ,['title'=>'Home']);
        }

        return view("auth.login",['title'=>'Login']);
    }

    public function login(Request $request) {
         //validasi input yang dimasukan oleh user
         $validate = $request->validate([
            'email'=>['required'  , 'max:50'],
            'password'=>['required' , 'min:8']
         ]);

         if(Auth::attempt($validate)){
             return redirect()->route("home.view");
         } 

         return back()->withErrors(['message'=>'Authentication failed']);
          
    }

    public function showRegister() {
                //cek jika user sudah register maka user tidak bisa register lagi 
                if(Auth::check()) {
                    return redirect()->route("home.view",['title'=>'Home']);
                }
                return view("auth.register",['title'=>'Register']);
    }

    public function register(Request $request) {

        $validator = $request->validate([
            'username'=>['required'],
            'display_name'=>['required'],
            'email'=>['required' , 'unique:users'],
            'password'=>['required' , 'min:8'],
            'confirm'=>['required', 'same:password' , 'min:8']
        ]);

        //cek apakah name user mengandung tag atau tidak
        if(!str_contains($request->display_name , "@")) {
             return back()->withErrors(['message'=>'please input valid username']);
        } 

        $created = User::create([
            'username'=>$request->username,
            'display_name'=>$request->display_name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
  
        //mengecek apakah data sudah di buat atau belum
        if($created) {
            return redirect()->route("auth.login.view");
        }

        return back()->withErrors(['message'=>'Failed to create account']);
    }

    public function logout() {
        Auth::logout();

        return redirect()->route('auth.login.view');
    }
}
