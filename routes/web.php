<?php

use App\Http\Controllers\Home\CommentController;
use Illuminate\Support\Facades\Route;

//controller
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Home\TweetsController;
use App\Http\Controllers\Home\ProfileController;
use App\Http\Controllers\Home\SearchController;
use App\Http\Controllers\Home\LikeController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//home preffix
Route::group(['prefix'=>'/'] , function($router) {
     Route::get('/' , [HomeController::class, 'showHome'])->name("home.view");
     Route::get('/profile' , [HomeController::class,'showProfile'])->name('home.profile.view');
     Route::get('/tweet' , [HomeController::class,'showCreateTweets'])->name('home.createTweet.view');

});

//prefix untuk authentikasi
Route::group(['prefix' => '/auth'], function($router) {
    Route::get("/" ,[AuthenticationController::class, 'showLogin'])->name('auth.login.view');
    Route::get("/register", [AuthenticationController::class, 'showRegister'])->name('auth.register.view');

    Route::post('/', [AuthenticationController::class, 'login'])->name("auth.login");
    Route::post("/register" , [AuthenticationController::class, 'register'])->name('auth.register');
    Route::post('/logout' , [AuthenticationController::class,'logout'])->name('auth.logout');
});

//prefix tweets

Route::group(['prefix'=>'/tweet'] , function($router) {
    Route::get('all' , [TweetsController::class, 'tweet'])->name('tweet.all');
    Route::get('/{id}' , [TweetsController::class,'detail_tweet'])->name('tweet.detail');
    Route::post('create' , [TweetsController::class, 'create_tweet'])->name('tweet.create');
});

//preffix profile

Route::group(['prefix' => '/profile'] , function($router) {
    Route::get('/tweet',[ProfileController::class, 'userTweet'])->name('profile.tweet');
    Route::delete('/tweet/delete/{id}' , [ProfileController::class,'delete_tweet'])->name('profile.tweet.delete');
    Route::post("/tweet/update/{id}", [ProfileController::class, 'update_tweet'])->name('profile.tweet.update');
    Route::post('/update' , [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/update/avatar' , [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

});

//prefix comment
Route::group(['prefix' => '/comment' ]  , function($router) {
     Route::get('all/{id}' , [CommentController::class, 'comment'])->name('comment.all');
     Route::post('create' , [CommentController::class, 'createComment'])->name('comment.create');
     Route::post('/delete/{id}' , [CommentController::class,'deleteComment'])->name('comment.delete');
     Route::post('/update' , [CommentController::class, 'updateComment'])->name('comment.update');
});

//search prefix 
Route::group(['prefix'=>'/search'] , function($router) {
     Route::get('/' , [SearchController::class, 'search'])->name('search');
});

//like prefix 

Route::post('/like' , [LikeController::class, 'like']);