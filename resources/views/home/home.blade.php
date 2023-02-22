@extends("layouts.layout")

@section("links")

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />


@endsection

@section("content")

<div class="w-full flex">
    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
    <!-- Modal untuk comment -->
    <div id="ex1" class="modal">
        <h4 class="text-center font-bold text-xl">Update Profile</h4>
        <form id="form-update-profile" action="{{ route('profile.update') }}" method="POST" class="mt-5 flex flex-col gap-y-2">
            @csrf
            <input type="text" name='username'  value="{{ Auth::user()->username }}" class="w-full border border-gray-200 py-2 px-3 rounded-md outline-none">
            <input type="text" name='display_name'  value="{{ Auth::user()->display_name }}" class="w-full border border-gray-200 py-2 px-3 rounded-md outline-none">
            <input type="email" name='email'  value="{{ Auth::user()->email }}" class="w-full border border-gray-200 py-2 px-3 rounded-md outline-none">
            <textarea  name="bio" class="w-full h-[80px] outline-none border border-gray-200 rounded-md">
              {{ Auth::user()->bio }}
            </textarea>
            <button type="submit" class="w-full bg-blue-400 text-white font-semibold mt-7 rounded-md text-sm py-2">Update</button>
        </form>
      </div>
      <!-- Modal untuk comment -->
     @include("components.sidebar")
     <section class="main w-[55%] h-screen overflow-y-scroll py-7 px-10">
        <div class="w-full flex items-center gap-x-5">
          <div class="relative flex-1">
            <i class="ri-search-line absolute top-2 left-2 text-gray-400"></i>
            <input placeholder="Find Tweets" id="search-input" type="text" class="pl-8 w-full outline-none rounded-full py-2 pr-3 bg-gray-100">
          </div>
           @if(Auth::user()->media != null)
           <img class="w-[36px] h-[36px] rounded-full" src="{{ asset('storage/profil_user/' . Auth::user()->media) }}">
           @else 
           <span class="font-bold w-[44px] h-[44px] text-xl uppercase rounded-full bg-blue-400 flex items-center justify-center text-white">
            {{ substr(Auth::user()->username , 0,1) }}
        </span>
           @endif
        </div>
        <div class="tweet-container py-5"></div>
        <div class="comment-container"></div>
     </section>
     <section class="helper-layout w-[25%] border-l border-gray-300 h-screen">
        <div class="trendings py-5 px-5">
            <h4 class="font-bold text-xl mb-7">Trending</h4>
            <div class="w-full py-3 px-3 bg-gray-100">
                <p>#Javascript</p>
            </div>
            <div class="w-full py-3 px-3 my-1 bg-gray-100">
                <p>#Javascript</p>
            </div>
            <div class="w-full py-3 px-3 bg-gray-100">
                <p>#Javascript</p>
            </div>
        </div>
     </section>
</div>

@endsection

@section("js")

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>

 $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

    $(document).ready(function() {
        //template html untuk post tweet
        function templateHtml(tweet , idx){
           return `
           <div class="w-full mt-5">
            <div class="flex items-center ">
                <img class="w-[50px] h-[50px] rounded-full" src="http://127.0.0.1:8000/storage/profil_user/${tweet.user.media}">
                <div class="flex-1 ml-4">
                    <h4 class="font-semibold text-lg">${tweet.user.username}</h4>
                    <p class="text-gray-500 mt-0">${tweet.user.display_name}</p>
                </div>
            </div>
            <p class="mt-4">${tweet.tweet} <a href="javascript:void(0)">
                <span class="text-blue-400 font-medium ml-3">${
                    tweet.tag.split(" ").map((data,idx) => {
                        return `#${data}`
                    }).join(" ")
                }</span></a> <span class="font-medium text-sm font-normal text-gray-500 ml-3"> ${new Date(tweet.created_at).toDateString()}</span></p>
            <img src="http://127.0.0.1:8000/storage/tweet_image/${tweet.media}" class="w-full h-[350px] rounded-md mt-3">
            <div class="flex items-center gap-x-7 mt-5">
             <div class="flex items-center text-[#545454]">
                
                ${tweet?.likes.length > 0 && tweet?.likes[idx]?.user_id == $('input[name="id"]').val() ? `<i data-tweet="${tweet.id}" class="like-tweet-btn cursor-pointer text-red-400 ri-heart-3-fill text-2xl"></i>` : `<i data-tweet="${tweet.id}" class="like-tweet-btn cursor-pointer  ri-heart-3-line text-2xl"></i>`}
                <p class="ml-4">${tweet.likes.length}</p>
             </div>
             <a href="http://127.0.0.1:8000/tweet/${tweet.id}" class="flex items-center text-[#545454]">
                <i class="ri-chat-3-line text-2xl"></i>
                <p class="ml-4">${tweet.comments.length}</p>
             </a>
             <div class="flex items-center text-[#545454]">
                <i class="ri-share-forward-box-line text-2xl"></i>
                <p class="ml-4">21</p>
             </div>
            </div>
        </div>
           `
        }

        //function untuk menampilkan seluruh post tweet
        function showPosts() {
            $.ajax({
             type:'GET',
             url:'http://127.0.0.1:8000/tweet/all',
             beforeSend:function(response){},
             success:function(response){
                  let temp = '';

          response.map((tweet, idx) => {
              temp += templateHtml(tweet, idx);
          });

          $('.tweet-container').html(temp);
             }
         });

        }

        showPosts();

        $(document).on('click' , '.like-tweet-btn', function(e) {
            $.ajax({
                 type:'POST',
                 url:`http://127.0.0.1:8000/like`,
                 data:{
                    user_id:$('input[name="id"]').val(),
                    tweet_id:$(this).attr('data-tweet')
                 },
                 success:function(){
                     showPosts();
                 }
            })
        });
       
         $(document).on('keyup' ,'#search-input' , function(e) {
              if(e.keyCode === 13) {
                 $.ajax({
                     type:'GET',
                     url:`http://127.0.0.1:8000/search?query=${e.target.value}`,
                     cache:false,
                     beforeSend:function(){
                         $('.tweet-container').html(`
                            <div class="w-full text-center py-10">
                                 <h3 class="text-2xl text-gray-800 font-bold">Loading..</h3>
                            </div>
                         `)
                     },
                     success:function(response){
                        if(response.tweets.length === 0){
                           alert('no result found!');
                            showPosts();
                        } 

                        let temp = ``;

                        response.tweets.map((tweet, idx) => {
                            temp+=templateHtml(tweet);
                        });

                        $('.tweet-container').html(temp);

                        if(response.comments.length === 0){
                           return $('.comment-container').css({display:"none"});
                        }


                        let tempComment = '';

                        response.comments.map((data, idx) => {
                             tempComment += `
                             <div class="flex items-center">
                        <div class="flex items-start">
                            <img class="w-[40px] h-[40px] mt-2 rounded-full" src="http://127.0.0.1:8000/storage/profil_user/${data.user.media}">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-x-3">
                                    <h5 class="font-semibold">${data.user.display_name}</h5>
                                    <p class="text-gray-500 text-sm">${data.tag.split(" ").map((item)=>`#${item}`).join(" ")}</p>
                                     ${$('#id_user').val() == data.user_id ?  
                                     `
                                       <div class="flex items-center ml-3">
                                        <button data-id="${data.id}" class="delete-btn ml-3 text-red-400 mr-auto"><i class="ri-delete-bin-7-line text-md"></i></button>
                                        <button data-comment="${data.id},${data.comment} , ${data.tag}" class="edit-btn ml-3 text-blue-400 mr-auto"><i class="ri-edit-line"></i></button>

                                        </div>
                                     ` : ''}
                                  
                                </div>
                                <p class="text-sm">${data.comment}</p>
                                ${data.media != null && data.media != '' ? `
                                     <img src="http://127.0.0.1:8000/storage/comment_image/${data.media}" class="w-full h-[100px] rounded-md mt-2">
                                    ` : ''} 
                            </div>
                        </div>
                     </div>
                             `;
                        });

                        $('.comment-container').html(tempComment);

                     }
                 })
              }
         })
    });
</script>


@endsection 